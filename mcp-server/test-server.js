#!/usr/bin/env node

/**
 * Test script untuk memverifikasi MCP server berjalan dengan baik
 */

import { exec } from "child_process";
import { promisify } from "util";

const execAsync = promisify(exec);

console.log("🧪 Testing Laravel LMS MCP Server...\n");

const tests = [
    {
        name: "Node.js Version Check",
        run: async () => {
            const { stdout } = await execAsync("node --version");
            const version = stdout.trim();
            const majorVersion = parseInt(version.slice(1).split(".")[0]);
            if (majorVersion >= 18) {
                return { success: true, message: `✅ Node.js ${version} (OK)` };
            }
            return {
                success: false,
                message: `❌ Node.js ${version} (Requires >= 18.0.0)`,
            };
        },
    },
    {
        name: "PHP Version Check",
        run: async () => {
            try {
                const { stdout } = await execAsync("php --version");
                const version = stdout.split("\n")[0];
                return { success: true, message: `✅ ${version}` };
            } catch (error) {
                return { success: false, message: "❌ PHP not found" };
            }
        },
    },
    {
        name: "Laravel Check",
        run: async () => {
            try {
                const { stdout } = await execAsync("php artisan --version", {
                    cwd: "..",
                });
                return { success: true, message: `✅ ${stdout.trim()}` };
            } catch (error) {
                return { success: false, message: "❌ Laravel not found" };
            }
        },
    },
    {
        name: "MCP SDK Check",
        run: async () => {
            try {
                await import("@modelcontextprotocol/sdk/server/index.js");
                return { success: true, message: "✅ MCP SDK installed" };
            } catch (error) {
                return { success: false, message: "❌ MCP SDK not found" };
            }
        },
    },
    {
        name: "Project Structure Check",
        run: async () => {
            try {
                const fs = await import("fs/promises");
                const path = await import("path");
                await fs.access(path.join("..", "app", "Models"));
                return { success: true, message: "✅ Laravel structure valid" };
            } catch (error) {
                return { success: false, message: "❌ Invalid Laravel structure" };
            }
        },
    },
];

async function runTests() {
    let passed = 0;
    let failed = 0;

    for (const test of tests) {
        console.log(`Running: ${test.name}...`);
        try {
            const result = await test.run();
            console.log(result.message);
            if (result.success) {
                passed++;
            } else {
                failed++;
            }
        } catch (error) {
            console.log(`❌ ${test.name}: ${error.message}`);
            failed++;
        }
        console.log();
    }

    console.log("═".repeat(50));
    console.log(`\n📊 Test Results:`);
    console.log(`   Passed: ${passed}`);
    console.log(`   Failed: ${failed}`);
    console.log(`   Total:  ${tests.length}\n`);

    if (failed === 0) {
        console.log("🎉 All tests passed! MCP server is ready to use.\n");
        console.log("Next steps:");
        console.log("1. Configure Cursor/Claude Desktop with the MCP server");
        console.log("2. Restart Cursor/Claude Desktop");
        console.log("3. Start using the tools!\n");
        console.log("See INSTALL.md for configuration instructions.");
    } else {
        console.log("⚠️  Some tests failed. Please fix the issues above.\n");
    }
}

runTests().catch(console.error);

