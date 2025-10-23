<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Landing Page Settings
            $table->boolean('show_landing_page')->default(true)->after('is_active');

            // Hero Section
            $table->string('hero_title')->nullable()->after('show_landing_page');
            $table->string('hero_subtitle')->nullable()->after('hero_title');
            $table->text('hero_description')->nullable()->after('hero_subtitle');
            $table->string('hero_image')->nullable()->after('hero_description');
            $table->string('hero_cta_text')->default('Get Started')->after('hero_image');
            $table->string('hero_cta_link')->nullable()->after('hero_cta_text');

            // About Section
            $table->text('about_title')->nullable()->after('hero_cta_link');
            $table->text('about_content')->nullable()->after('about_title');
            $table->string('about_image')->nullable()->after('about_content');

            // Features Section (JSON array)
            $table->json('features')->nullable()->after('about_image');

            // Statistics Section
            $table->json('statistics')->nullable()->after('features');

            // Contact Section
            $table->text('contact_address')->nullable()->after('statistics');
            $table->string('contact_phone')->nullable()->after('contact_address');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('contact_whatsapp')->nullable()->after('contact_email');

            // Social Media
            $table->string('social_facebook')->nullable()->after('contact_whatsapp');
            $table->string('social_instagram')->nullable()->after('social_facebook');
            $table->string('social_twitter')->nullable()->after('social_instagram');
            $table->string('social_youtube')->nullable()->after('social_twitter');

            // SEO
            $table->string('meta_title')->nullable()->after('social_youtube');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'show_landing_page',
                'hero_title',
                'hero_subtitle',
                'hero_description',
                'hero_image',
                'hero_cta_text',
                'hero_cta_link',
                'about_title',
                'about_content',
                'about_image',
                'features',
                'statistics',
                'contact_address',
                'contact_phone',
                'contact_email',
                'contact_whatsapp',
                'social_facebook',
                'social_instagram',
                'social_twitter',
                'social_youtube',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};
