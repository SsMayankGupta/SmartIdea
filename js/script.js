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

loadQuestion();
