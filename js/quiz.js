const questions = [
{
question:"Which bin should plastic bottles go into?",
options:["Organic Waste","Plastic/Recycling Bin","General Waste","E-Waste"],
answer:1
},
{
question:"Where should fruit peels be disposed?",
options:["Plastic Bin","Organic/Wet Waste","Glass Bin","E-Waste"],
answer:1
},
{
question:"Old batteries belong in which category?",
options:["Plastic Waste","General Waste","E-Waste","Organic Waste"],
answer:2
},
{
question:"Why should plastic containers be flattened?",
options:["Save space","Decoration","Burn faster","No reason"],
answer:0
},
{
question:"Which action helps reduce landfill waste?",
options:["Mix waste","Recycle properly","Throw anywhere","Ignore waste"],
answer:1
}
];

let currentIndex = 0;
let score = 0;
let answered = false;

const questionText = document.getElementById("questionText");
const optionsContainer = document.getElementById("optionsContainer");
const feedback = document.getElementById("feedback");
const nextBtn = document.getElementById("nextBtn");
const questionNumber = document.getElementById("questionNumber");
const progressFill = document.getElementById("progressFill");
const scoreValue = document.getElementById("scoreValue");
const finalResult = document.getElementById("finalResult");
const scoreText = document.getElementById("scoreText");
const restartBtn = document.getElementById("restartBtn");

function loadQuestion(){

answered = false;
feedback.innerHTML = "";
optionsContainer.innerHTML = "";

let q = questions[currentIndex];

questionText.innerHTML = q.question;
questionNumber.innerHTML = (currentIndex+1)+" / "+questions.length;

let progress = (currentIndex/questions.length)*100;
progressFill.style.width = progress + "%";

q.options.forEach((option,index)=>{

let btn = document.createElement("button");
btn.classList.add("option-btn");
btn.innerText = option;

btn.onclick = ()=>{

if(answered) return;

answered = true;

let correct = q.answer;
let buttons = document.querySelectorAll(".option-btn");

if(index===correct){

btn.classList.add("correct");
feedback.innerHTML="✔ Correct!";
score += 10;
scoreValue.innerHTML = score;

}
else{

btn.classList.add("wrong");
buttons[correct].classList.add("correct");

feedback.innerHTML="✖ Wrong! Correct answer: "+q.options[correct];

}

};

optionsContainer.appendChild(btn);

});

}

nextBtn.onclick = ()=>{

if(!answered){
feedback.innerHTML="Please select an answer!";
return;
}

currentIndex++;

if(currentIndex < questions.length){

loadQuestion();

}
else{

showFinalScore();

}

};

function showFinalScore(){

questionText.innerHTML="🎉 Quiz Completed!";
optionsContainer.innerHTML="";
nextBtn.style.display="none";
feedback.innerHTML="";

progressFill.style.width="100%";

finalResult.style.display="block";

scoreText.innerHTML="You earned "+score+" Green Points 🌱";

launchConfetti();

}

/* ================= CONFETTI ================= */

function launchConfetti(){

const canvas = document.getElementById("confettiCanvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let pieces=[];

for(let i=0;i<120;i++){

pieces.push({
x:Math.random()*canvas.width,
y:Math.random()*canvas.height-400,
size:8,
speed:2+Math.random()*3
});

}

let animation;

function draw(){

ctx.clearRect(0,0,canvas.width,canvas.height);

pieces.forEach(p=>{

ctx.fillStyle="#2ecc71";
ctx.fillRect(p.x,p.y,p.size,p.size);

p.y+=p.speed;

});

animation = requestAnimationFrame(draw);

}

draw();

/* stop after 5 seconds */

setTimeout(()=>{
cancelAnimationFrame(animation);
ctx.clearRect(0,0,canvas.width,canvas.height);
},5000);

}

/* ================= RESTART QUIZ ================= */

restartBtn.onclick = ()=>{

currentIndex = 0;
score = 0;
answered = false;

scoreValue.innerHTML = "0";

nextBtn.style.display = "inline-block";

finalResult.style.display = "none";

progressFill.style.width = "0%";

loadQuestion();

};

/* start quiz */

window.onload = loadQuestion;