<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <i class="fas fa-paint-brush text-purple-600 mr-2"></i>
                    Landing Page Editor - {{ $school->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Customize your school's landing page</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.landing-page.preview', $school) }}" target="_blank"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-2"></i>Preview
                </a>
                <a href="{{ route('admin.schools.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.landing-page.update', $school) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tab Navigation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" role="tablist">
                            <button type="button"
                                class="tab-button border-b-2 border-purple-500 text-purple-600 py-4 px-1 font-medium text-sm"
                                data-tab="general">
                                <i class="fas fa-cog mr-2"></i>General
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="hero">
                                <i class="fas fa-image mr-2"></i>Hero Section
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="about">
                                <i class="fas fa-info-circle mr-2"></i>About Section
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="features">
                                <i class="fas fa-star mr-2"></i>Features
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="statistics">
                                <i class="fas fa-chart-bar mr-2"></i>Statistics
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="contact">
                                <i class="fas fa-envelope mr-2"></i>Contact & Social
                            </button>
                            <button type="button"
                                class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 font-medium text-sm"
                                data-tab="seo">
                                <i class="fas fa-search mr-2"></i>SEO
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- General Tab -->
                        <div class="tab-content" data-tab-content="general">
                            <h3 class="text-lg font-semibold mb-4">General Settings</h3>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_landing_page" value="1"
                                        {{ $school->show_landing_page ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Enable landing page (uncheck to show
                                        default Laravel page)</span>
                                </label>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            When enabled, visitors will see your custom landing page instead of the
                                            default Laravel welcome page.
                                            Make sure to fill in the sections below to create an engaging landing page.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Section Tab -->
                        <div class="tab-content hidden" data-tab-content="hero">
                            <h3 class="text-lg font-semibold mb-4">Hero Section</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="hero_title" class="block text-sm font-medium text-gray-700 mb-2">Hero
                                        Title</label>
                                    <input type="text" name="hero_title" id="hero_title"
                                        value="{{ old('hero_title', $school->hero_title) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="Welcome to Our School">
                                </div>

                                <div>
                                    <label for="hero_subtitle" class="block text-sm font-medium text-gray-700 mb-2">Hero
                                        Subtitle</label>
                                    <input type="text" name="hero_subtitle" id="hero_subtitle"
                                        value="{{ old('hero_subtitle', $school->hero_subtitle) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="Learn, Grow, Succeed">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="hero_description" class="block text-sm font-medium text-gray-700 mb-2">Hero
                                    Description</label>
                                <textarea name="hero_description" id="hero_description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="A brief description of your school and what makes it special...">{{ old('hero_description', $school->hero_description) }}</textarea>
                            </div>

                            <div class="mt-4">
                                <label for="hero_image" class="block text-sm font-medium text-gray-700 mb-2">Hero
                                    Background Image</label>
                                @if ($school->hero_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $school->hero_image) }}"
                                            alt="Current hero image" class="h-32 rounded">
                                    </div>
                                @endif
                                <input type="file" name="hero_image" id="hero_image" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-sm text-gray-500">Recommended size: 1920x1080px</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label for="hero_cta_text"
                                        class="block text-sm font-medium text-gray-700 mb-2">Call-to-Action Button
                                        Text</label>
                                    <input type="text" name="hero_cta_text" id="hero_cta_text"
                                        value="{{ old('hero_cta_text', $school->hero_cta_text ?? 'Get Started') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>

                                <div>
                                    <label for="hero_cta_link"
                                        class="block text-sm font-medium text-gray-700 mb-2">Call-to-Action Button
                                        Link</label>
                                    <input type="text" name="hero_cta_link" id="hero_cta_link"
                                        value="{{ old('hero_cta_link', $school->hero_cta_link) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="/register or https://...">
                                </div>
                            </div>
                        </div>

                        <!-- About Section Tab -->
                        <div class="tab-content hidden" data-tab-content="about">
                            <h3 class="text-lg font-semibold mb-4">About Section</h3>

                            <div class="mb-4">
                                <label for="about_title" class="block text-sm font-medium text-gray-700 mb-2">About
                                    Title</label>
                                <input type="text" name="about_title" id="about_title"
                                    value="{{ old('about_title', $school->about_title) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="About Our School">
                            </div>

                            <div class="mb-4">
                                <label for="about_content" class="block text-sm font-medium text-gray-700 mb-2">About
                                    Content</label>
                                <textarea name="about_content" id="about_content" rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="Tell visitors about your school, its mission, vision, and values...">{{ old('about_content', $school->about_content) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="about_image" class="block text-sm font-medium text-gray-700 mb-2">About
                                    Image (Optional)</label>
                                @if ($school->about_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $school->about_image) }}"
                                            alt="Current about image" class="h-32 rounded">
                                    </div>
                                @endif
                                <input type="file" name="about_image" id="about_image" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-sm text-gray-500">Recommended size: 600x400px</p>
                            </div>
                        </div>

                        <!-- Features Tab -->
                        <div class="tab-content hidden" data-tab-content="features">
                            <h3 class="text-lg font-semibold mb-4">Features Section</h3>
                            <p class="text-sm text-gray-600 mb-4">Highlight the key features of your school (max 6
                                recommended)</p>

                            <div id="features-container">
                                @php
                                    $features = old('features', $school->features ?? []);
                                    if (empty($features)) {
                                        $features = [
                                            [
                                                'icon' => 'fa-graduation-cap',
                                                'title' => 'Quality Education',
                                                'description' => 'High-quality learning materials',
                                            ],
                                            [
                                                'icon' => 'fa-users',
                                                'title' => 'Interactive Learning',
                                                'description' => 'Engage with peers and teachers',
                                            ],
                                            [
                                                'icon' => 'fa-certificate',
                                                'title' => 'Certification',
                                                'description' => 'Get certified upon completion',
                                            ],
                                        ];
                                    }
                                @endphp

                                @foreach ($features as $index => $feature)
                                    <div class="feature-item bg-gray-50 p-4 rounded-lg mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium">Feature #{{ $index + 1 }}</h4>
                                            <button type="button" onclick="removeFeature(this)"
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Font
                                                    Awesome Icon</label>
                                                <input type="text" name="features[{{ $index }}][icon]"
                                                    value="{{ $feature['icon'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                    placeholder="fa-graduation-cap">
                                                <p class="text-xs text-gray-500 mt-1">Search icons at <a
                                                        href="https://fontawesome.com/icons" target="_blank"
                                                        class="text-purple-600">FontAwesome</a></p>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                                <input type="text" name="features[{{ $index }}][title]"
                                                    value="{{ $feature['title'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                                <input type="text"
                                                    name="features[{{ $index }}][description]"
                                                    value="{{ $feature['description'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" onclick="addFeature()"
                                class="mt-4 bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>Add Feature
                            </button>
                        </div>

                        <!-- Statistics Tab -->
                        <div class="tab-content hidden" data-tab-content="statistics">
                            <h3 class="text-lg font-semibold mb-4">Statistics Section</h3>
                            <p class="text-sm text-gray-600 mb-4">Show impressive numbers about your school (max 4
                                recommended)</p>

                            <div id="statistics-container">
                                @php
                                    $statistics = old('statistics', $school->statistics ?? []);
                                    if (empty($statistics)) {
                                        $statistics = [
                                            ['label' => 'Active Students', 'value' => '1000+'],
                                            ['label' => 'Courses', 'value' => '50+'],
                                            ['label' => 'Teachers', 'value' => '30+'],
                                            ['label' => 'Success Rate', 'value' => '95%'],
                                        ];
                                    }
                                @endphp

                                @foreach ($statistics as $index => $stat)
                                    <div class="statistic-item bg-gray-50 p-4 rounded-lg mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium">Statistic #{{ $index + 1 }}</h4>
                                            <button type="button" onclick="removeStatistic(this)"
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                                                <input type="text" name="statistics[{{ $index }}][label]"
                                                    value="{{ $stat['label'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                    placeholder="Active Students">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                                                <input type="text" name="statistics[{{ $index }}][value]"
                                                    value="{{ $stat['value'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                    placeholder="1000+">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" onclick="addStatistic()"
                                class="mt-4 bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>Add Statistic
                            </button>
                        </div>

                        <!-- Contact & Social Tab -->
                        <div class="tab-content hidden" data-tab-content="contact">
                            <h3 class="text-lg font-semibold mb-4">Contact Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_email"
                                        class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="contact_email" id="contact_email"
                                        value="{{ old('contact_email', $school->contact_email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>

                                <div>
                                    <label for="contact_phone"
                                        class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="text" name="contact_phone" id="contact_phone"
                                        value="{{ old('contact_phone', $school->contact_phone) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>

                                <div>
                                    <label for="contact_whatsapp"
                                        class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                                    <input type="text" name="contact_whatsapp" id="contact_whatsapp"
                                        value="{{ old('contact_whatsapp', $school->contact_whatsapp) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="+62xxx">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="contact_address"
                                        class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea name="contact_address" id="contact_address" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('contact_address', $school->contact_address) }}</textarea>
                                </div>
                            </div>

                            <h3 class="text-lg font-semibold mt-8 mb-4">Social Media</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook URL
                                    </label>
                                    <input type="url" name="social_facebook" id="social_facebook"
                                        value="{{ old('social_facebook', $school->social_facebook) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="https://facebook.com/yourschool">
                                </div>

                                <div>
                                    <label for="social_instagram"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram URL
                                    </label>
                                    <input type="url" name="social_instagram" id="social_instagram"
                                        value="{{ old('social_instagram', $school->social_instagram) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="https://instagram.com/yourschool">
                                </div>

                                <div>
                                    <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter URL
                                    </label>
                                    <input type="url" name="social_twitter" id="social_twitter"
                                        value="{{ old('social_twitter', $school->social_twitter) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="https://twitter.com/yourschool">
                                </div>

                                <div>
                                    <label for="social_youtube" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-youtube text-red-600 mr-2"></i>YouTube URL
                                    </label>
                                    <input type="url" name="social_youtube" id="social_youtube"
                                        value="{{ old('social_youtube', $school->social_youtube) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="https://youtube.com/@yourschool">
                                </div>
                            </div>
                        </div>

                        <!-- SEO Tab -->
                        <div class="tab-content hidden" data-tab-content="seo">
                            <h3 class="text-lg font-semibold mb-4">SEO Settings</h3>
                            <p class="text-sm text-gray-600 mb-4">Optimize your landing page for search engines</p>

                            <div class="mb-4">
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta
                                    Title</label>
                                <input type="text" name="meta_title" id="meta_title"
                                    value="{{ old('meta_title', $school->meta_title) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="Your School Name - Quality Education">
                                <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                            </div>

                            <div class="mb-4">
                                <label for="meta_description"
                                    class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="A brief description of your school for search engines...">{{ old('meta_description', $school->meta_description) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                            </div>

                            <div class="mb-4">
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta
                                    Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords"
                                    value="{{ old('meta_keywords', $school->meta_keywords) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="education, online learning, school, courses">
                                <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('admin.schools.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded">
                        <i class="fas fa-save mr-2"></i>Save Landing Page
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tab functionality
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', () => {
                    const tabName = button.dataset.tab;

                    // Update button styles
                    document.querySelectorAll('.tab-button').forEach(btn => {
                        btn.classList.remove('border-purple-500', 'text-purple-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-purple-500', 'text-purple-600');

                    // Show/hide content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.querySelector(`[data-tab-content="${tabName}"]`).classList.remove('hidden');
                });
            });

            // Feature management
            let featureIndex = {{ count($features) }};

            function addFeature() {
                const container = document.getElementById('features-container');
                const html = `
                <div class="feature-item bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium">Feature #${featureIndex + 1}</h4>
                        <button type="button" onclick="removeFeature(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Font Awesome Icon</label>
                            <input type="text" name="features[${featureIndex}][icon]" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="fa-graduation-cap">
                            <p class="text-xs text-gray-500 mt-1">Search icons at <a href="https://fontawesome.com/icons" target="_blank" class="text-purple-600">FontAwesome</a></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="features[${featureIndex}][title]" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="features[${featureIndex}][description]" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', html);
                featureIndex++;
            }

            function removeFeature(button) {
                button.closest('.feature-item').remove();
            }

            // Statistic management
            let statisticIndex = {{ count($statistics) }};

            function addStatistic() {
                const container = document.getElementById('statistics-container');
                const html = `
                <div class="statistic-item bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium">Statistic #${statisticIndex + 1}</h4>
                        <button type="button" onclick="removeStatistic(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                            <input type="text" name="statistics[${statisticIndex}][label]" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="Active Students">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                            <input type="text" name="statistics[${statisticIndex}][value]" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="1000+">
                        </div>
                    </div>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', html);
                statisticIndex++;
            }

            function removeStatistic(button) {
                button.closest('.statistic-item').remove();
            }
        </script>
    @endpush
</x-app-layout>
