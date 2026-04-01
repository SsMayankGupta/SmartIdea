//Report Waste
// Auto Generate Report ID
document.getElementById("reportId").value =
  "WR-" + Math.floor(100000 + Math.random() * 900000);

// Auto Fill Date & Time
document.getElementById("dateTime").value =
  new Date().toLocaleString();

// Image Preview
document.getElementById("wasteImage").addEventListener("change", function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById("preview");

  if (file) {
    const reader = new FileReader();
    reader.onload = function() {
      preview.src = reader.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
});

// Submit Function
function submitReport() {
  alert("Waste Report Submitted Successfully!");
} 