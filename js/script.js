const questions = [
  {
    question: "Which bin should plastic bottles go into?",
    options: ["Organic Bin", "Recycling Bin", "General Waste", "E-Waste"],
    answer: 1
  },
  {
    question: "Where should fruit peels be disposed?",
    options: ["Organic Bin", "Plastic Bin", "E-Waste", "Glass Bin"],
    answer: 0
  },
  {
    question: "Old batteries should be thrown in?",
    options: ["General Waste", "Plastic Bin", "E-Waste Center", "Organic Bin"],
    answer: 2
  },
  {
    question: "What should you do before recycling bottles?",
    options: ["Burn them", "Rinse them", "Break them", "Throw anywhere"],
    answer: 1
  },
  {
    question: "Which waste should never mix with food waste?",
    options: ["Plastic", "Vegetables", "Paper", "Leaves"],
    answer: 0
  }
];

let currentQuestion = 0;

const questionText = document.getElementById("questionText");
const optionsContainer = document.getElementById("optionsContainer");
const feedback = document.getElementById("feedback");
const nextBtn = document.getElementById("nextBtn");

function loadQuestion() {
  if (!questionText || !optionsContainer) return;
  
  feedback.innerHTML = "";
  optionsContainer.innerHTML = "";

  const q = questions[currentQuestion];
  questionText.textContent = q.question;

  q.options.forEach((option, index) => {
    const btn = document.createElement("button");
    btn.textContent = option;
    btn.classList.add("option-btn");

    btn.onclick = () => checkAnswer(index, btn);

    optionsContainer.appendChild(btn);
  });
}

function checkAnswer(selectedIndex, button) {
  const correctIndex = questions[currentQuestion].answer;
  const buttons = document.querySelectorAll(".option-btn");

  buttons.forEach(btn => btn.disabled = true);

  if (selectedIndex === correctIndex) {
    button.classList.add("correct");
    feedback.innerHTML = "✔ Correct Answer!";
    feedback.style.color = "green";
  } else {
    button.classList.add("wrong");
    buttons[correctIndex].classList.add("correct");
    feedback.innerHTML = "❌ Wrong! Correct answer is: " + questions[currentQuestion].options[correctIndex];
    feedback.style.color = "red";
  }
}

if (nextBtn) {
  nextBtn.onclick = function() {
    currentQuestion++;
    if (currentQuestion < questions.length) {
      loadQuestion();
    } else {
      questionText.textContent = "🎉 Quiz Completed!";
      optionsContainer.innerHTML = "";
      feedback.innerHTML = "";
      nextBtn.style.display = "none";
    }
  };
}

// Load quiz only if elements exist
if (questionText && optionsContainer) {
  loadQuestion();
}

/**
 * ================= SIGNUP FUNCTION =================
 * Connects to database via signup_handler.php
 */
function signup() {
  const name = document.getElementById('name').value.trim();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  const errorMsg = document.getElementById('errorMsg');
  const successMsg = document.getElementById('successMsg');
  const signupBtn = document.getElementById('signupBtn');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');
  
  // Reset messages
  errorMsg.style.display = 'none';
  successMsg.style.display = 'none';
  
  // Validation
  if (!name || !email || !password || !confirmPassword) {
    errorMsg.textContent = 'Please fill in all fields';
    errorMsg.style.display = 'block';
    return false;
  }
  
  if (password.length < 6) {
    errorMsg.textContent = 'Password must be at least 6 characters';
    errorMsg.style.display = 'block';
    return false;
  }
  
  if (password !== confirmPassword) {
    errorMsg.textContent = 'Passwords do not match';
    errorMsg.style.display = 'block';
    return false;
  }
  
  // Show loading state
  signupBtn.disabled = true;
  spinner.style.display = 'inline-block';
  btnText.textContent = 'Creating Account...';
  
  // Prepare data
  const data = {
    full_name: name,
    email: email,
    password: password
  };
  
  // Send to PHP handler
  fetch('signup_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      // Show success message
      successMsg.textContent = result.message || 'Account created successfully!';
      successMsg.style.display = 'block';
      
      // Store user in localStorage for frontend use
      if (result.user) {
        localStorage.setItem('user', JSON.stringify(result.user));
        localStorage.setItem('logged_in', 'true');
      }
      
      // Redirect after 2 seconds
      setTimeout(() => {
        if (result.redirect) {
          window.location.href = result.redirect;
        } else {
          window.location.href = '../pages/dashboard.html';
        }
      }, 2000);
    } else {
      // Show error
      errorMsg.textContent = result.error || 'Registration failed. Please try again.';
      errorMsg.style.display = 'block';
      
      // Reset button
      signupBtn.disabled = false;
      spinner.style.display = 'none';
      btnText.textContent = 'CREATE ACCOUNT';
    }
  })
  .catch(error => {
    console.error('Signup error:', error);
    errorMsg.textContent = 'Network error. Please check your connection and try again.';
    errorMsg.style.display = 'block';
    
    // Reset button
    signupBtn.disabled = false;
    spinner.style.display = 'none';
    btnText.textContent = 'CREATE ACCOUNT';
  });
  
  return false;
}

/**
 * ================= LOGIN FUNCTION =================
 */
function login() {
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  
  const errorMsg = document.getElementById('errorMsg');
  const loginBtn = document.getElementById('loginBtn');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');
  
  // Reset messages
  if (errorMsg) errorMsg.style.display = 'none';
  
  // Validation
  if (!email || !password) {
    if (errorMsg) {
      errorMsg.textContent = 'Please enter email and password';
      errorMsg.style.display = 'block';
    }
    return false;
  }
  
  // Show loading state
  if (loginBtn) loginBtn.disabled = true;
  if (spinner) spinner.style.display = 'inline-block';
  if (btnText) btnText.textContent = 'Logging in...';
  
  // Prepare data
  const data = {
    email: email,
    password: password
  };
  
  // Send to PHP handler
  fetch('login_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      // Store user in localStorage
      if (result.user) {
        localStorage.setItem('user', JSON.stringify(result.user));
        localStorage.setItem('logged_in', 'true');
      }
      
      // Redirect
      window.location.href = result.redirect || '../pages/dashboard.html';
    } else {
      // Show error
      if (errorMsg) {
        errorMsg.textContent = result.error || 'Login failed. Please check your credentials.';
        errorMsg.style.display = 'block';
      }
      
      // Reset button
      if (loginBtn) loginBtn.disabled = false;
      if (spinner) spinner.style.display = 'none';
      if (btnText) btnText.textContent = 'LOGIN';
    }
  })
  .catch(error => {
    console.error('Login error:', error);
    if (errorMsg) {
      errorMsg.textContent = 'Network error. Please check your connection and try again.';
      errorMsg.style.display = 'block';
    }
    
    // Reset button
    if (loginBtn) loginBtn.disabled = false;
    if (spinner) spinner.style.display = 'none';
    if (btnText) btnText.textContent = 'LOGIN';
  });
  
  return false;
}

/**
 * ================= SESSION CHECK =================
 * Check if user is logged in
 */
function checkAuth() {
  const user = localStorage.getItem('user');
  const loggedIn = localStorage.getItem('logged_in');
  
  // Update UI if user is logged in
  if (user && loggedIn === 'true') {
    const userData = JSON.parse(user);
    
    // Update navbar if elements exist
    const navActions = document.querySelector('.nav-actions');
    if (navActions) {
      navActions.innerHTML = `
        <span class="text-white font-medium">Welcome, ${userData.full_name}</span>
        <a href="#" onclick="logout()" class="logout-btn">Logout</a>
      `;
    }
  }
}

/**
 * ================= LOGOUT FUNCTION =================
 */
function logout() {
  // Clear localStorage
  localStorage.removeItem('user');
  localStorage.removeItem('logged_in');
  
  // Call PHP logout
  fetch('../auth/logout.php', { method: 'POST' })
    .then(() => {
      window.location.href = '../index.html';
    })
    .catch(() => {
      window.location.href = '../index.html';
    });
}

// Check auth on page load
document.addEventListener('DOMContentLoaded', checkAuth);
