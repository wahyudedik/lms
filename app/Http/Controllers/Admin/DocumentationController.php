<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    /**
     * List all documentation
     */
    public function index()
    {
        $docs = $this->getDocumentationList();

        return view('admin.documentation.index', compact('docs'));
    }

    /**
     * Show specific documentation
     */
    public function show($slug)
    {
        $docs = $this->getDocumentationList();

        // Find the document by slug
        $doc = collect($docs)->firstWhere('slug', $slug);

        if (!$doc) {
            abort(404, 'Documentation not found');
        }

        // Check if file exists
        if (!File::exists($doc['path'])) {
            abort(404, 'Documentation file not found');
        }

        // Read content
        $content = File::get($doc['path']);

        // Parse markdown to HTML
        $html = $this->parseMarkdown($content);

        return view('admin.documentation.show', compact('doc', 'html', 'docs'));
    }

    /**
     * Get list of all documentation files
     */
    private function getDocumentationList()
    {
        $docsPath = base_path('docs');

        if (!File::exists($docsPath)) {
            return [];
        }

        $files = File::files($docsPath);

        $docs = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilename();
                $slug = Str::slug(pathinfo($filename, PATHINFO_FILENAME));

                // Read first line as title
                $content = File::get($file->getPathname());
                preg_match('/^#\s+(.+)$/m', $content, $matches);
                $title = $matches[1] ?? pathinfo($filename, PATHINFO_FILENAME);

                // Get category from filename
                $category = $this->getCategoryFromFilename($filename);

                // Get icon based on category
                $icon = $this->getIconForCategory($category);

                // Get description (first paragraph after title)
                preg_match('/^#.+\n\n(.+?)(\n\n|$)/s', $content, $descMatches);
                $description = $descMatches[1] ?? 'Documentation guide';
                $description = strip_tags($description);
                $description = Str::limit($description, 150);

                $docs[] = [
                    'slug' => $slug,
                    'filename' => $filename,
                    'title' => $title,
                    'description' => $description,
                    'category' => $category,
                    'icon' => $icon,
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                ];
            }
        }

        // Sort by category and title
        usort($docs, function ($a, $b) {
            if ($a['category'] === $b['category']) {
                return strcmp($a['title'], $b['title']);
            }
            return strcmp($a['category'], $b['category']);
        });

        return $docs;
    }

    /**
     * Get category from filename
     */
    private function getCategoryFromFilename($filename)
    {
        $name = strtolower($filename);

        if (str_contains($name, 'certificate')) {
            return 'Certificates';
        }

        if (str_contains($name, 'offline')) {
            return 'Offline Mode';
        }

        if (str_contains($name, 'forum')) {
            return 'Forum';
        }

        if (str_contains($name, 'exam') || str_contains($name, 'cbt')) {
            return 'Exams';
        }

        if (str_contains($name, 'landing')) {
            return 'Landing Page';
        }

        return 'General';
    }

    /**
     * Get icon for category
     */
    private function getIconForCategory($category)
    {
        return match ($category) {
            'Certificates' => 'fa-certificate',
            'Offline Mode' => 'fa-wifi-slash',
            'Forum' => 'fa-comments',
            'Exams' => 'fa-file-alt',
            'Landing Page' => 'fa-home',
            default => 'fa-book',
        };
    }

    /**
     * Parse markdown to HTML
     */
    private function parseMarkdown($content)
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(false); // Allow HTML in markdown
        return $parsedown->text($content);
    }
}
