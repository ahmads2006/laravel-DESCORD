@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900 to-purple-900 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $specialization->name }} Quiz</h2>
                    <div class="text-gray-300 text-sm mt-1">Test your knowledge in {{ $specialization->name }}</div>
                </div>
                <div class="mt-2 md:mt-0">
                    <div class="text-gray-300 text-sm">Time Remaining:</div>
                    <div id="timer" class="font-mono text-yellow-400 text-lg font-bold">05:00</div>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                <i class="fas fa-exclamation-circle mr-1"></i>
                <span id="warning-message">Don't leave this page or you'll lose your progress</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-gray-900 h-2">
            <div id="progress-bar" class="h-full bg-green-500 transition-all duration-300" style="width: 0%"></div>
        </div>

        <form id="quiz-form" action="{{ route('quiz.submit', $test->id) }}" method="POST" class="p-6">
            @csrf
            
            <!-- Hidden field for elapsed time -->
            <input type="hidden" name="elapsed_time" id="elapsed_time" value="0">
            
            @foreach($questions as $index => $question)
                <div class="mb-8 p-6 bg-gray-700 rounded-xl shadow-md question-container" id="question-{{ $question->id }}">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-medium text-white">
                            <span class="bg-indigo-600 text-white rounded-full w-8 h-8 inline-flex items-center justify-center mr-3">Q{{ $index + 1 }}</span>
                            {{ $question->question_text }}
                        </h3>
                        @if($question->question_type == 'multiple')
                            <span class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full">Multiple Choice</span>
                        @endif
                    </div>
                    
                    @if($question->image_url)
                        <div class="mb-6">
                            <img src="{{ asset('storage/' . $question->image_url) }}" alt="Question Image" class="rounded-lg max-h-64 mx-auto">
                        </div>
                    @endif
                    
                    <div class="space-y-3">
                        @foreach($question->choices as $choiceIndex => $choice)
                            <div class="choice-container flex items-center p-4 rounded-lg hover:bg-gray-600 transition-all duration-200 cursor-pointer border border-gray-600 hover:border-gray-500" 
                                 onclick="selectAnswer({{ $question->id }}, {{ $choice->id }})"
                                 id="choice-{{ $choice->id }}">
                                <div class="flex items-center flex-1">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 mr-4 font-medium">
                                        {{ chr(65 + $choiceIndex) }}
                                    </div>
                                    <input id="c_{{ $choice->id }}" 
                                           type="{{ $question->question_type == 'multiple' ? 'checkbox' : 'radio' }}" 
                                           name="answers[{{ $question->id }}]{{ $question->question_type == 'multiple' ? '[]' : '' }}" 
                                           value="{{ $choice->id }}"
                                           class="w-5 h-5 text-blue-600 bg-gray-800 border-gray-600 focus:ring-blue-500 hidden"
                                           onchange="updateProgress()">
                                    <label for="c_{{ $choice->id }}" class="ml-2 text-gray-200 w-full cursor-pointer">
                                        {{ $choice->choice_text }}
                                        @if($choice->is_correct && auth()->user()->isAdmin())
                                            <span class="ml-2 text-green-400 text-xs">(Correct Answer)</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="selected-indicator hidden">
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Question navigation -->
                    <div class="mt-6 pt-4 border-t border-gray-600 flex justify-between">
                        @if($index > 0)
                            <button type="button" onclick="scrollToQuestion({{ $index - 1 }})" 
                                    class="text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Previous Question
                            </button>
                        @else
                            <div></div>
                        @endif
                        
                        @if($index < count($questions) - 1)
                            <button type="button" onclick="scrollToQuestion({{ $index + 1 }})" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                                Next Question <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        @else
                            <div></div>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="mt-8 p-6 bg-gray-900 rounded-xl">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <div class="text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span id="answered-count">0</span> of {{ count($questions) }} questions answered
                        </div>
                        <div class="text-sm text-gray-400 mt-1">
                            Please review your answers before submitting
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" onclick="showReviewModal()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <i class="fas fa-list-check mr-2"></i>Review Answers
                        </button>
                        
                        <button type="submit" 
                                class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Quiz
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Review Your Answers</h3>
                <button onclick="hideReviewModal()" class="text-gray-300 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="review-questions">
                    <!-- Questions will be populated here -->
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-900 flex justify-end">
                <button onclick="hideReviewModal()" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                    Continue Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Quiz Configuration
const TOTAL_TIME = 300; // 5 minutes in seconds
const TOTAL_QUESTIONS = {{ count($questions) }};
let timeLeft = TOTAL_TIME;
let elapsedTime = 0;
let timerInterval;
let isSubmitting = false;
let userActivity = {
    tabChanges: 0,
    focusLost: false,
    lastActivity: Date.now()
};

// Initialize quiz
document.addEventListener('DOMContentLoaded', function() {
    initTimer();
    initActivityMonitor();
    updateProgress();
    
    // Auto-save every 30 seconds
    setInterval(autoSaveProgress, 30000);
    
    // Warn before leaving
    window.addEventListener('beforeunload', handleBeforeUnload);
});

// Timer functions
function initTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        timeLeft--;
        elapsedTime++;
        document.getElementById('elapsed_time').value = elapsedTime;
        
        updateTimerDisplay();
        
        // Color coding for time
        if (timeLeft <= 60) {
            document.getElementById('timer').classList.remove('text-yellow-400');
            document.getElementById('timer').classList.add('text-red-400', 'animate-pulse');
        } else if (timeLeft <= 120) {
            document.getElementById('timer').classList.add('text-yellow-400');
            document.getElementById('timer').classList.remove('text-red-400', 'animate-pulse');
        }
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitQuiz('Time is up! Submitting your answers...');
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Anti-cheat measures
function initActivityMonitor() {
    // Track tab visibility
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            userActivity.tabChanges++;
            userActivity.focusLost = true;
            
            if (userActivity.tabChanges > 2) {
                showWarning("Multiple tab changes detected. Further changes may result in quiz termination.");
            }
            
            if (userActivity.tabChanges > 5) {
                submitQuiz("Excessive tab switching detected. Quiz submitted.");
            }
        }
        userActivity.lastActivity = Date.now();
    });
    
    // Track copy/paste
    document.addEventListener('copy', (e) => {
        e.preventDefault();
        showWarning("Copying is disabled during the quiz.");
    });
    
    document.addEventListener('paste', (e) => {
        e.preventDefault();
        showWarning("Pasting is disabled during the quiz.");
    });
    
    // Track right-click
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        showWarning("Right-click is disabled during the quiz.");
    });
    
    // Track keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U
        if (e.keyCode === 123 || 
            (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) ||
            (e.ctrlKey && e.keyCode === 85)) {
            e.preventDefault();
            showWarning("Developer tools are disabled during the quiz.");
        }
        
        // Disable Print Screen
        if (e.keyCode === 44) {
            e.preventDefault();
            showWarning("Screen capture is disabled.");
        }
    });
}

function showWarning(message) {
    const warningEl = document.getElementById('warning-message');
    warningEl.textContent = message;
    warningEl.classList.add('text-red-400');
    
    setTimeout(() => {
        warningEl.textContent = "Don't leave this page or you'll lose your progress";
        warningEl.classList.remove('text-red-400');
    }, 3000);
}

// Answer selection
function selectAnswer(questionId, choiceId) {
    const question = document.querySelector(`[name="answers[${questionId}][]"]`) ? 
                     document.querySelectorAll(`[name="answers[${questionId}][]"]`) :
                     document.querySelector(`[name="answers[${questionId}]"]`);
    
    if (question instanceof NodeList) {
        // Multiple select
        const checkbox = document.getElementById(`c_${choiceId}`);
        checkbox.checked = !checkbox.checked;
        
        const choiceContainer = document.getElementById(`choice-${choiceId}`);
        const selectedIndicator = choiceContainer.querySelector('.selected-indicator');
        
        if (checkbox.checked) {
            choiceContainer.classList.add('bg-indigo-900', 'border-indigo-500');
            selectedIndicator.classList.remove('hidden');
        } else {
            choiceContainer.classList.remove('bg-indigo-900', 'border-indigo-500');
            selectedIndicator.classList.add('hidden');
        }
    } else {
        // Single select
        const allChoices = document.querySelectorAll(`[name="answers[${questionId}]"]`);
        allChoices.forEach(choice => {
            const choiceContainer = document.getElementById(`choice-${choice.value}`);
            if (choiceContainer) {
                choiceContainer.classList.remove('bg-indigo-900', 'border-indigo-500');
                choiceContainer.querySelector('.selected-indicator').classList.add('hidden');
            }
        });
        
        const selectedChoice = document.getElementById(`c_${choiceId}`);
        selectedChoice.checked = true;
        
        const choiceContainer = document.getElementById(`choice-${choiceId}`);
        choiceContainer.classList.add('bg-indigo-900', 'border-indigo-500');
        choiceContainer.querySelector('.selected-indicator').classList.remove('hidden');
    }
    
    updateProgress();
    userActivity.lastActivity = Date.now();
}

// Progress tracking
function updateProgress() {
    let answered = 0;
    const questionIds = [];
    
    // Collect all question IDs
    document.querySelectorAll('.question-container').forEach(container => {
        const id = container.id.replace('question-', '');
        questionIds.push(id);
    });
    
    // Count answered questions
    questionIds.forEach(id => {
        const checkboxes = document.querySelectorAll(`input[name="answers[${id}][]"]:checked`);
        const radio = document.querySelector(`input[name="answers[${id}]"]:checked`);
        
        if ((checkboxes && checkboxes.length > 0) || radio) {
            answered++;
        }
    });
    
    // Update UI
    const percentage = (answered / TOTAL_QUESTIONS) * 100;
    document.getElementById('progress-bar').style.width = `${percentage}%`;
    document.getElementById('answered-count').textContent = answered;
    
    // Color code progress bar
    const progressBar = document.getElementById('progress-bar');
    progressBar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');
    
    if (percentage < 30) {
        progressBar.classList.add('bg-red-500');
    } else if (percentage < 70) {
        progressBar.classList.add('bg-yellow-500');
    } else {
        progressBar.classList.add('bg-green-500');
    }
}

// Navigation
function scrollToQuestion(index) {
    const questions = document.querySelectorAll('.question-container');
    if (questions[index]) {
        questions[index].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Review modal
function showReviewModal() {
    const modal = document.getElementById('review-modal');
    const questionsContainer = document.getElementById('review-questions');
    questionsContainer.innerHTML = '';
    
    document.querySelectorAll('.question-container').forEach((container, index) => {
        const questionId = container.id.replace('question-', '');
        const questionText = container.querySelector('h3').textContent.replace(/^Q\d+\.\s*/, '');
        
        let answerStatus = 'Not answered';
        let statusColor = 'text-red-400';
        
        const checkboxes = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`);
        const radio = document.querySelector(`input[name="answers[${questionId}]"]:checked`);
        
        if (checkboxes.length > 0 || radio) {
            answerStatus = 'Answered';
            statusColor = 'text-green-400';
        }
        
        const questionEl = document.createElement('div');
        questionEl.className = 'bg-gray-700 p-4 rounded-lg';
        questionEl.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-white">Q${index + 1}</span>
                <span class="${statusColor} text-sm">${answerStatus}</span>
            </div>
            <div class="text-gray-300 text-sm truncate">${questionText.substring(0, 50)}...</div>
            <button onclick="scrollToQuestion(${index}); hideReviewModal();" 
                    class="mt-3 text-indigo-400 hover:text-indigo-300 text-sm w-full text-left">
                <i class="fas fa-arrow-right mr-1"></i> Go to question
            </button>
        `;
        
        questionsContainer.appendChild(questionEl);
    });
    
    modal.classList.remove('hidden');
}

function hideReviewModal() {
    document.getElementById('review-modal').classList.add('hidden');
}

// Auto-save
function autoSaveProgress() {
    const formData = new FormData(document.getElementById('quiz-form'));
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("quiz.autosave", $test->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Progress auto-saved');
          }
      });
}

// Form submission
function handleBeforeUnload(e) {
    if (!isSubmitting) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
}

function submitQuiz(message) {
    if (isSubmitting) return;
    
    isSubmitting = true;
    clearInterval(timerInterval);
    
    if (message) {
        alert(message);
    }
    
    // Remove beforeunload handler
    window.removeEventListener('beforeunload', handleBeforeUnload);
    
    // Submit form
    document.getElementById('quiz-form').submit();
}

// Keyboard shortcuts for navigation
document.addEventListener('keydown', function(e) {
    // Ctrl + Enter to submit
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        submitQuiz();
    }
    
    // Escape to close review modal
    if (e.key === 'Escape') {
        hideReviewModal();
    }
});

// Add some visual feedback on answer selection
document.querySelectorAll('.choice-container').forEach(container => {
    container.addEventListener('click', function() {
        this.classList.add('active');
        setTimeout(() => {
            this.classList.remove('active');
        }, 200);
    });
});
</script>

<style>
.choice-container.active {
    transform: scale(0.98);
    transition: transform 0.2s;
}

#progress-bar {
    transition: width 0.5s ease-in-out;
}

.question-container {
    scroll-margin-top: 20px;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.animate-pulse {
    animation: pulse 1s infinite;
}
</style>
@endsection