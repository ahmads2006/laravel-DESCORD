@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-block p-3 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 mb-4 shadow-lg">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4 bg-gradient-to-r from-white to-indigo-200 bg-clip-text text-transparent">
                Select Your Language
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Choose the language you're most comfortable with. This will be used throughout your learning journey.
            </p>
            
            <!-- Progress Indicator -->
            <div class="mt-8 max-w-md mx-auto">
                <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                    <span>Step 1 of 3</span>
                    <span>Language Selection</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full w-1/3"></div>
                </div>
            </div>
        </div>

        <!-- Language Selection Cards -->
        <div class="max-w-3xl mx-auto">
            <form id="language-form" action="{{ route('language.store') }}" method="POST">
                @csrf
                
                <!-- Language Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($languages as $language)
                        <div class="language-card group relative">
                            <input id="lang_{{ $language->id }}" 
                                   type="radio" 
                                   name="language_id" 
                                   value="{{ $language->id }}" 
                                   class="absolute opacity-0 h-0 w-0 peer"
                                   {{ old('language_id') == $language->id ? 'checked' : '' }}
                                   onchange="updateSelectedLanguage('{{ $language->name }}')">
                            
                            <label for="lang_{{ $language->id }}" 
                                   class="flex flex-col items-center p-6 bg-gray-800 rounded-xl border-2 border-gray-700 cursor-pointer transition-all duration-300 hover:border-indigo-500 hover:bg-gray-750 hover:transform hover:-translate-y-1 peer-checked:border-indigo-500 peer-checked:bg-gradient-to-br peer-checked:from-gray-800 peer-checked:to-indigo-900/20 peer-checked:shadow-2xl peer-checked:shadow-indigo-900/30 h-full">
                                
                                <!-- Language Flag/Icon -->
                                <div class="w-16 h-16 mb-4 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    @if($language->flag_emoji)
                                        <span class="text-2xl">{{ $language->flag_emoji }}</span>
                                    @else
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 5a1 1 0 100 2h1a2 2 0 011.732 1H7a1 1 0 100 2h2.732A2 2 0 018 11H7a1 1 0 00-.707 1.707l3 3a1 1 0 001.414-1.414l-1.483-1.484A4.008 4.008 0 0011.874 10H13a1 1 0 100-2h-1.126a3.976 3.976 0 00-.41-1H13a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                
                                <!-- Language Name -->
                                <h3 class="text-xl font-bold text-white mb-2 text-center">{{ $language->name }}</h3>
                                
                                <!-- Language Code -->
                                <div class="px-3 py-1 bg-gray-900 rounded-full text-gray-300 text-sm mb-4">
                                    {{ strtoupper($language->code) }}
                                </div>
                                
                                <!-- Language Stats (if available) -->
                                @if(isset($language->learner_count))
                                    <div class="mt-2 text-sm text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ number_format($language->learner_count) }} learners
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Checkmark Indicator -->
                                <div class="mt-4 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Popular Badge -->
                            @if($language->is_popular)
                                <div class="absolute -top-2 -right-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                    Popular
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Selected Language Preview -->
                <div id="selected-preview" class="hidden mb-8 p-6 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl border border-gray-700">
                    <div class="flex items-center">
                        <div id="selected-icon" class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center mr-4">
                            <span id="selected-emoji" class="text-xl"></span>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400">Selected Language</div>
                            <div id="selected-name" class="text-2xl font-bold text-white"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Error Display -->
                @error('language_id')
                    <div class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg flex items-start">
                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-red-400">Selection Required</div>
                            <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
                        </div>
                    </div>
                @enderror

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <!-- Back Button -->
                    <a href="{{ url()->previous() }}" 
                       class="flex items-center px-6 py-3 text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </a>
                    
                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submit-btn"
                            class="group relative w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <span class="flex items-center justify-center">
                            Continue
                            <svg id="submit-icon" class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <span id="selected-language-text" class="block text-sm font-normal mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            Please select a language to continue
                        </span>
                    </button>
                </div>
                
                <!-- Language Selection Tips -->
                <div class="mt-12 p-6 bg-gray-800/50 rounded-xl border border-gray-700">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Tips for choosing your language
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-900/50 rounded-lg">
                            <div class="text-sm text-gray-400">Choose the language you're most comfortable thinking and learning in</div>
                        </div>
                        <div class="p-3 bg-gray-900/50 rounded-lg">
                            <div class="text-sm text-gray-400">You can change this later in your profile settings if needed</div>
                        </div>
                        <div class="p-3 bg-gray-900/50 rounded-lg">
                            <div class="text-sm text-gray-400">Content and quizzes will be displayed in your selected language</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Language selection functionality
function updateSelectedLanguage(languageName) {
    const submitBtn = document.getElementById('submit-btn');
    const submitIcon = document.getElementById('submit-icon');
    const selectedText = document.getElementById('selected-language-text');
    const preview = document.getElementById('selected-preview');
    const selectedName = document.getElementById('selected-name');
    
    // Enable submit button
    submitBtn.disabled = false;
    
    // Update button text and preview
    selectedText.textContent = `Continue with ${languageName}`;
    selectedText.classList.remove('opacity-0');
    selectedName.textContent = languageName;
    
    // Show preview
    preview.classList.remove('hidden');
    
    // Animate button
    submitBtn.classList.remove('disabled:opacity-50');
    
    // Add confetti effect on first selection
    if (!window.hasSelectedLanguage) {
        window.hasSelectedLanguage = true;
        triggerConfetti();
    }
}

// Simple confetti effect
function triggerConfetti() {
    const confettiCount = 30;
    const colors = ['#6366f1', '#8b5cf6', '#10b981', '#3b82f6'];
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.borderRadius = '50%';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.top = '-10px';
        confetti.style.opacity = '0.9';
        confetti.style.zIndex = '9999';
        confetti.style.pointerEvents = 'none';
        
        document.body.appendChild(confetti);
        
        // Animation
        const animation = confetti.animate([
            { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
            { transform: `translateY(${window.innerHeight}px) rotate(${Math.random() * 360}deg)`, opacity: 0 }
        ], {
            duration: 1000 + Math.random() * 2000,
            easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
        });
        
        animation.onfinish = () => confetti.remove();
    }
}

// Form submission handling
document.getElementById('language-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        </span>
    `;
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const radios = document.querySelectorAll('input[type="radio"]');
    const currentIndex = Array.from(radios).findIndex(radio => radio.checked);
    
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = (currentIndex + 1) % radios.length;
        radios[nextIndex].checked = true;
        radios[nextIndex].dispatchEvent(new Event('change'));
        radios[nextIndex].focus();
    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = (currentIndex - 1 + radios.length) % radios.length;
        radios[prevIndex].checked = true;
        radios[prevIndex].dispatchEvent(new Event('change'));
        radios[prevIndex].focus();
    } else if (e.key === 'Enter' && currentIndex !== -1) {
        document.getElementById('language-form').submit();
    }
});

// Initialize form with previously selected language if any
document.addEventListener('DOMContentLoaded', function() {
    const selectedRadio = document.querySelector('input[type="radio"]:checked');
    if (selectedRadio) {
        const languageName = selectedRadio.closest('.language-card').querySelector('h3').textContent;
        updateSelectedLanguage(languageName);
    }
    
    // Add hover effect to cards
    const cards = document.querySelectorAll('.language-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<style>
.language-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.language-card label {
    min-height: 220px;
}

input[type="radio"]:checked + label {
    box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.1), 0 10px 10px -5px rgba(99, 102, 241, 0.04);
}

input[type="radio"]:focus + label {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
}

/* Glow effect on hover */
.language-card:hover label {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
}

/* Smooth transitions */
* {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* Custom scrollbar for the page */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #4f46e5;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6366f1;
}
</style>
@endsection