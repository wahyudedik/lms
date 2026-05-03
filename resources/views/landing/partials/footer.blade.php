<footer id="contact" class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            {{-- About Section --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="h-12 w-auto">
                    <h3 class="text-xl font-bold">{{ $school->name }}</h3>
                </div>
                @if ($school->about_content)
                    <p class="text-gray-400 mb-4">
                        {{ Str::limit($school->about_content, 150) }}
                    </p>
                @else
                    <p class="text-gray-400 mb-4">
                        Platform pembelajaran online terpercaya untuk meningkatkan keterampilan dan pengetahuan Anda.
                    </p>
                @endif
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-xl font-bold mb-4">Link Cepat</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#home" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>Beranda
                        </a>
                    </li>
                    <li>
                        <a href="#courses" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>Kursus
                        </a>
                    </li>
                    <li>
                        <a href="#services" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>Layanan
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Dashboard
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Login
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Daftar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-xl font-bold mb-4">Hubungi Kami</h3>
                <ul class="space-y-3">
                    @if ($school->contact_address)
                        <li class="flex items-start gap-3 text-gray-400">
                            <i class="fas fa-map-marker-alt mt-1"></i>
                            <span>{{ $school->contact_address }}</span>
                        </li>
                    @endif

                    @if ($school->contact_phone)
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-gray-400"></i>
                            <a href="tel:{{ $school->contact_phone }}"
                                class="text-gray-400 hover:text-white transition-colors">
                                {{ $school->contact_phone }}
                            </a>
                        </li>
                    @endif

                    @if ($school->contact_email)
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-gray-400"></i>
                            <a href="mailto:{{ $school->contact_email }}"
                                class="text-gray-400 hover:text-white transition-colors">
                                {{ $school->contact_email }}
                            </a>
                        </li>
                    @endif

                    @if ($school->contact_whatsapp)
                        <li class="flex items-center gap-3">
                            <i class="fab fa-whatsapp text-gray-400"></i>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->contact_whatsapp) }}"
                                target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                {{ $school->contact_whatsapp }}
                            </a>
                        </li>
                    @endif

                    @if (!$school->contact_address && !$school->contact_phone && !$school->contact_email && !$school->contact_whatsapp)
                        <li class="text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i>
                            Hubungi Kami
                        </li>
                    @endif
                </ul>

                {{-- Social Media --}}
                @if ($school->social_facebook || $school->social_instagram || $school->social_twitter || $school->social_youtube)
                    <div class="mt-6">
                        <h4 class="font-semibold mb-3">Ikuti Kami</h4>
                        <div class="flex gap-3">
                            @if ($school->social_facebook)
                                <a href="{{ $school->social_facebook }}" target="_blank"
                                    class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif

                            @if ($school->social_instagram)
                                <a href="{{ $school->social_instagram }}" target="_blank"
                                    class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif

                            @if ($school->social_twitter)
                                <a href="{{ $school->social_twitter }}" target="_blank"
                                    class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-400 transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif

                            @if ($school->social_youtube)
                                <a href="{{ $school->social_youtube }}" target="_blank"
                                    class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $school->name }}. All rights reserved.</p>
        </div>
    </div>
</footer>
