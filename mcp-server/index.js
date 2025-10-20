#!/usr/bin/env node

/**
 * Laravel LMS MCP Server
 * Model Context Protocol server for interacting with Laravel application
 */

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import { exec } from "child_process";
import { promisify } from "util";
import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const execAsync = promisify(exec);
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PROJECT_ROOT = path.resolve(__dirname, "..");

class LaravelMCPServer {
  constructor() {
    this.server = new Server(
      {
        name: "laravel-lms-server",
        version: "1.0.0",
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );

    this.setupHandlers();
  }

  setupHandlers() {
    // List available tools
    this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
      tools: [
        {
          name: "run_artisan",
          description: "Execute Laravel Artisan commands",
          inputSchema: {
            type: "object",
            properties: {
              command: {
                type: "string",
                description: "Artisan command to execute (e.g., 'migrate', 'make:model User')",
              },
            },
            required: ["command"],
          },
        },
        {
          name: "read_model",
          description: "Read Laravel model file contents",
          inputSchema: {
            type: "object",
            properties: {
              model_name: {
                type: "string",
                description: "Name of the model (e.g., 'User', 'Course')",
              },
            },
            required: ["model_name"],
          },
        },
        {
          name: "read_controller",
          description: "Read Laravel controller file contents",
          inputSchema: {
            type: "object",
            properties: {
              controller_name: {
                type: "string",
                description: "Name of the controller (e.g., 'UserController')",
              },
            },
            required: ["controller_name"],
          },
        },
        {
          name: "list_routes",
          description: "List all registered Laravel routes",
          inputSchema: {
            type: "object",
            properties: {},
          },
        },
        {
          name: "read_migration",
          description: "Read migration file contents",
          inputSchema: {
            type: "object",
            properties: {
              migration_name: {
                type: "string",
                description: "Name or pattern of the migration file",
              },
            },
            required: ["migration_name"],
          },
        },
        {
          name: "read_config",
          description: "Read Laravel configuration file",
          inputSchema: {
            type: "object",
            properties: {
              config_name: {
                type: "string",
                description: "Name of config file (e.g., 'app', 'database')",
              },
            },
            required: ["config_name"],
          },
        },
        {
          name: "run_test",
          description: "Run Laravel tests using Pest or PHPUnit",
          inputSchema: {
            type: "object",
            properties: {
              test_filter: {
                type: "string",
                description: "Optional test filter or specific test to run",
              },
            },
          },
        },
        {
          name: "create_model",
          description: "Create a new Laravel model with optional migration and controller",
          inputSchema: {
            type: "object",
            properties: {
              name: {
                type: "string",
                description: "Model name",
              },
              migration: {
                type: "boolean",
                description: "Create migration file",
                default: true,
              },
              controller: {
                type: "boolean",
                description: "Create controller",
                default: false,
              },
            },
            required: ["name"],
          },
        },
        {
          name: "database_query",
          description: "Execute database query using tinker",
          inputSchema: {
            type: "object",
            properties: {
              query: {
                type: "string",
                description: "PHP code to execute in tinker (e.g., 'User::count()')",
              },
            },
            required: ["query"],
          },
        },
        {
          name: "read_env",
          description: "Read environment configuration",
          inputSchema: {
            type: "object",
            properties: {},
          },
        },
      ],
    }));

    // Handle tool execution
    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      const { name, arguments: args } = request.params;

      try {
        switch (name) {
          case "run_artisan":
            return await this.runArtisan(args.command);
          
          case "read_model":
            return await this.readModel(args.model_name);
          
          case "read_controller":
            return await this.readController(args.controller_name);
          
          case "list_routes":
            return await this.listRoutes();
          
          case "read_migration":
            return await this.readMigration(args.migration_name);
          
          case "read_config":
            return await this.readConfig(args.config_name);
          
          case "run_test":
            return await this.runTest(args.test_filter);
          
          case "create_model":
            return await this.createModel(args.name, args.migration, args.controller);
          
          case "database_query":
            return await this.databaseQuery(args.query);
          
          case "read_env":
            return await this.readEnv();
          
          default:
            throw new Error(`Unknown tool: ${name}`);
        }
      } catch (error) {
        return {
          content: [
            {
              type: "text",
              text: `Error: ${error.message}`,
            },
          ],
        };
      }
    });
  }

  async runArtisan(command) {
    const { stdout, stderr } = await execAsync(
      `php artisan ${command}`,
      { cwd: PROJECT_ROOT }
    );
    return {
      content: [
        {
          type: "text",
          text: stdout || stderr,
        },
      ],
    };
  }

  async readModel(modelName) {
    const modelPath = path.join(PROJECT_ROOT, "app", "Models", `${modelName}.php`);
    const content = await fs.readFile(modelPath, "utf-8");
    return {
      content: [
        {
          type: "text",
          text: content,
        },
      ],
    };
  }

  async readController(controllerName) {
    const controllerPath = path.join(
      PROJECT_ROOT,
      "app",
      "Http",
      "Controllers",
      `${controllerName}.php`
    );
    const content = await fs.readFile(controllerPath, "utf-8");
    return {
      content: [
        {
          type: "text",
          text: content,
        },
      ],
    };
  }

  async listRoutes() {
    const { stdout } = await execAsync("php artisan route:list --json", {
      cwd: PROJECT_ROOT,
    });
    return {
      content: [
        {
          type: "text",
          text: stdout,
        },
      ],
    };
  }

  async readMigration(migrationName) {
    const migrationsDir = path.join(PROJECT_ROOT, "database", "migrations");
    const files = await fs.readdir(migrationsDir);
    const migrationFile = files.find((file) => file.includes(migrationName));
    
    if (!migrationFile) {
      throw new Error(`Migration not found: ${migrationName}`);
    }
    
    const content = await fs.readFile(
      path.join(migrationsDir, migrationFile),
      "utf-8"
    );
    return {
      content: [
        {
          type: "text",
          text: content,
        },
      ],
    };
  }

  async readConfig(configName) {
    const configPath = path.join(PROJECT_ROOT, "config", `${configName}.php`);
    const content = await fs.readFile(configPath, "utf-8");
    return {
      content: [
        {
          type: "text",
          text: content,
        },
      ],
    };
  }

  async runTest(testFilter = "") {
    const command = testFilter
      ? `php artisan test --filter=${testFilter}`
      : `php artisan test`;
    
    const { stdout, stderr } = await execAsync(command, {
      cwd: PROJECT_ROOT,
    });
    return {
      content: [
        {
          type: "text",
          text: stdout || stderr,
        },
      ],
    };
  }

  async createModel(name, migration = true, controller = false) {
    let command = `make:model ${name}`;
    if (migration) command += " -m";
    if (controller) command += " -c";
    
    return await this.runArtisan(command);
  }

  async databaseQuery(query) {
    const tempFile = path.join(PROJECT_ROOT, "temp_tinker.php");
    await fs.writeFile(tempFile, query);
    
    try {
      const { stdout, stderr } = await execAsync(
        `php artisan tinker < ${tempFile}`,
        { cwd: PROJECT_ROOT }
      );
      await fs.unlink(tempFile);
      return {
        content: [
          {
            type: "text",
            text: stdout || stderr,
          },
        ],
      };
    } catch (error) {
      await fs.unlink(tempFile).catch(() => {});
      throw error;
    }
  }

  async readEnv() {
    const envPath = path.join(PROJECT_ROOT, ".env");
    try {
      const content = await fs.readFile(envPath, "utf-8");
      // Mask sensitive information
      const masked = content.replace(
        /(PASSWORD|SECRET|KEY)=.*/gi,
        "$1=********"
      );
      return {
        content: [
          {
            type: "text",
            text: masked,
          },
        ],
      };
    } catch (error) {
      return {
        content: [
          {
            type: "text",
            text: "No .env file found",
          },
        ],
      };
    }
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error("Laravel LMS MCP Server running on stdio");
  }
}

const server = new LaravelMCPServer();
server.run().catch(console.error);

