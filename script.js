let isDropdownOpen = false;

function togglePassengerDropdown() {
  const dropdown = document.querySelector('.passenger-dropdown');
  isDropdownOpen = !isDropdownOpen;
  dropdown.classList.toggle('active', isDropdownOpen);
}

function redirectLogin() {
  alert("Anda perlu membuat akun terlebih dahulu.");
  window.location.href = 'login.html';
}

function perluAkun() {
  alert("Anda perlu membuat akun terlebih dahulu.");
  window.location.href = 'login.html';
}

function updateLayanan() {
  var selectValue = document.getElementById("layanan").value;
  document.getElementById("LayananInput").value = selectValue;
}

function updateJam() {
  var selectValue = document.getElementById("jamMasuk").value;
  document.getElementById("JamInput").value = selectValue;
}

function updateTujuan() {
  var selectValue = document.getElementById("pelabuhanAsal").value;
  document.getElementById("AsalInput").value = selectValue;
}

function updateAsal() {
  var selectValue = document.getElementById("pelabuhanTujuan").value;
  document.getElementById("TujuanInput").value = selectValue;
}

function updateBarang() {
  var selectValue = document.getElementById("jenisBarang").value;
  document.getElementById("BarangInput").value = selectValue;
}

function updateCargoAsal() {
  var selectValue = document.getElementById("pelabuhanAsalCargo").value;
  document.getElementById("CargoAsal").value = selectValue;
}

function updateCargoTujuan() {
  var selectValue = document.getElementById("pelabuhanTujuanCargo").value;
  document.getElementById("CargoTujuan").value = selectValue;
}

function increaseCount(type) {
  const countElement = document.getElementById(`${type}-count`);
  let count = parseInt(countElement.textContent, 10);
  count++;
  countElement.textContent = count;
  updatePassengerDetails();
}

function decreaseCount(type) {
  const countElement = document.getElementById(`${type}-count`);
  let count = parseInt(countElement.textContent, 10);
  if (count > 0) {
    count--;
    countElement.textContent = count;
    updatePassengerDetails();
  }
}

function updatePassengerDetails() {
  const dewasa = parseInt(document.getElementById('dewasa-count').textContent, 10);
  const anak = parseInt(document.getElementById('anak-count').textContent, 10);
  const bayi = parseInt(document.getElementById('bayi-count').textContent, 10);
  const lansia = parseInt(document.getElementById('lansia-count').textContent, 10);

  // Build the display text dynamically
  let passengerText = [];
  if (dewasa > 0) passengerText.push(`Dewasa: ${dewasa}`);
  if (anak > 0) passengerText.push(`Anak: ${anak}`);
  if (bayi > 0) passengerText.push(`Bayi: ${bayi}`);
  if (lansia > 0) passengerText.push(`Lansia: ${lansia}`);

  // Update the header display
  const passengerHeader = document.getElementById('passengerHeader');
  passengerHeader.textContent = passengerText.length > 0 ? passengerText.join(', ') : 'Jumlah Penumpang';

  // Update hidden inputs for form submission
  document.getElementById('dewasaCount').value = dewasa;
  document.getElementById('anakCount').value = anak;
  document.getElementById('bayiCount').value = bayi;
  document.getElementById('lansiaCount').value = lansia;
}

// Menutup dropdown ketika mengklik di luar
document.addEventListener('click', (e) => {
  const dropdown = document.querySelector('.passenger-select');
  if (!dropdown.contains(e.target) && isDropdownOpen) {
    togglePassengerDropdown();
  }
});

function toggleTicketDropdown() {
  const dropdown = document.querySelector('.ticket-dropdown');
  const body = document.body;
  
  if (dropdown.style.display === 'block') {
    dropdown.style.display = 'none';
    body.classList.remove('modal-open');
  } else {
    dropdown.style.display = 'block';
    body.classList.add('modal-open');
  }
}

function selectTicketType(ticketType) {
  document.getElementById('selectedTicketType').value = ticketType;
  const ticketHeader = document.getElementById('ticketHeader');
  ticketHeader.textContent = `${ticketType}`;
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
  const dropdown = document.querySelector('.ticket-dropdown');
  const ticketSelect = document.querySelector('.ticket-select');
  
  if (dropdown.style.display === 'block' && 
      !dropdown.contains(e.target) && 
      !ticketSelect.contains(e.target)) {
    toggleTicketDropdown();
  }
});

// Prevent closing when clicking inside modal
document.querySelector('.ticket-dropdown').addEventListener('click', (e) => {
  e.stopPropagation();
});

function switchBookingType(type) {
  const formPenumpang = document.getElementById('formPenumpang');
  const formCargo = document.getElementById('formCargo');
  const btnPenumpang = document.querySelector('.booking-type-btn:nth-child(1)');
  const btnCargo = document.querySelector('.booking-type-btn:nth-child(2)');

  if (type === 'penumpang') {
    formPenumpang.style.display = 'grid';
    formCargo.style.display = 'none';
    btnPenumpang.classList.add('active');
    btnCargo.classList.remove('active');
  } else if (type === 'cargo') {
    formPenumpang.style.display = 'none';
    formCargo.style.display = 'grid';
    btnPenumpang.classList.remove('active');
    btnCargo.classList.add('active');
  }
}

// Function to handle Passenger Form Submission
function handlePassengerSubmit() {
  // Collect Passenger Form Data
  const asal = document.getElementById('pelabuhanAsal').value;
  const tujuan = document.getElementById('pelabuhanTujuan').value;
  const tanggal = document.getElementById('tanggal').value;
  const jam = document.getElementById('jamMasuk').value;
  const layanan = document.getElementById('layanan').value;
  const dewasa = document.getElementById('dewasaCount').value;
  const anak = document.getElementById('anakCount').value;
  const bayi = document.getElementById('bayiCount').value;
  const lansia = document.getElementById('lansiaCount').value;
  const tipeTiket = document.getElementById('selectedTicketType').value;

  // Save Passenger Data to Session Storage
  sessionStorage.setItem('asal', asal);
  sessionStorage.setItem('tujuan', tujuan);
  sessionStorage.setItem('tanggal', tanggal);
  sessionStorage.setItem('jam', jam);
  sessionStorage.setItem('layanan', layanan);
  sessionStorage.setItem('dewasa', dewasa);
  sessionStorage.setItem('anak', anak);
  sessionStorage.setItem('bayi', bayi);
  sessionStorage.setItem('lansia', lansia);
  sessionStorage.setItem('tipeTiket', tipeTiket);

  // Redirect to ticketdetail.html
  window.location.href = 'ticketdetail.html';
}

// Function to handle Cargo Form Submission
function handleCargoSubmit() {
  // Collect Cargo Form Data
  const jenisBarang = document.getElementById('jenisBarang').value;
  const beratBarang = document.getElementById('beratBarang').value;
  const asalCargo = document.getElementById('pelabuhanAsalCargo').value;
  const tujuanCargo = document.getElementById('pelabuhanTujuanCargo').value;
  const tanggalCargo = document.getElementById('tanggalCargo').value;

  // Save Cargo Data to Session Storage
  sessionStorage.setItem('jenisBarang', jenisBarang);
  sessionStorage.setItem('beratBarang', beratBarang);
  sessionStorage.setItem('asalCargo', asalCargo);
  sessionStorage.setItem('tujuanCargo', tujuanCargo);
  sessionStorage.setItem('tanggalCargo', tanggalCargo);

  // Redirect to ticketdetail.html
  window.location.href = 'ticketdetail.html';
}


// Generate random 16-digit bank account number starting with 777
function generateBankAccount() {
  const randomNumbers = Math.floor(Math.random() * 10000000000000).toString().padStart(13, '0');
  return `777${randomNumbers}`;
}

// Toggle dropdown visibility
function togglePayDropdown(dropdownId) {
  const dropdown = document.getElementById(dropdownId);
  const isVisible = dropdown.style.display === 'block';
  dropdown.style.display = isVisible ? 'none' : 'block';

  // Generate bank account number when the bank dropdown is opened
  if (dropdownId === 'bankDropdown' && !isVisible) {
    document.getElementById('bankAccount').textContent = generateBankAccount();
  }
}

// Test Proceed Button
function proceedToNextStep() {
  alert('Proceeding to the next step...');
  window.location.href = "confirmation.html";
}

// Handle offer card click events
function openOfferDetails(offerTitle) {
  alert(`You selected: ${offerTitle}. For more details, contact us!`);
}

function checkPasswords() {
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm-password').value;

  if (password !== confirmPassword) {
    document.getElementById('confirm-password').setCustomValidity('Passwords do not match');
  } else {
    document.getElementById('confirm-password').setCustomValidity('');
  }
}

function openTab(event, tabName) {
  // Get all elements with class="tab-content" and hide them
  var tabContents = document.getElementsByClassName("tab-content");
  for (var i = 0; i < tabContents.length; i++) {
    tabContents[i].style.display = "none";
  }

  // Get all elements with class="tab-link" and remove the class "active"
  var tabLinks = document.getElementsByClassName("tab-link");
  for (var i = 0; i < tabLinks.length; i++) {
    tabLinks[i].classList.remove("active");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(tabName).style.display = "block";
  event.currentTarget.classList.add("active");
}

// validasi input

document.getElementById('formPenumpang').addEventListener('submit', function(event) {
  event.preventDefault(); // Mencegah form submit default

  const pelabuhanAsal = document.getElementById('pelabuhanAsal').value;
  const pelabuhanTujuan = document.getElementById('pelabuhanTujuan').value;
  const tanggal = document.getElementById('tanggal').value;
  const layanan = document.getElementById('layanan').value;
  const jumlahDewasa = parseInt(document.getElementById('dewasa-count').textContent, 10);
  const jumlahAnak = parseInt(document.getElementById('anak-count').textContent, 10);
  const jumlahBayi = parseInt(document.getElementById('bayi-count').textContent, 10);
  const jumlahLansia = parseInt(document.getElementById('lansia-count').textContent, 10);
  const tipeTiket = document.getElementById('selectedTicketType').value;

  if (!pelabuhanAsal || !pelabuhanTujuan || !tanggal || !layanan || 
      (jumlahDewasa + jumlahAnak + jumlahBayi + jumlahLansia === 0) || !tipeTiket) {
      alert('Harap lengkapi semua field sebelum melanjutkan!');
      return;
  }

  const data = {
      pelabuhanAsal,
      pelabuhanTujuan,
      tanggal,
      layanan,
      tipeTiket,
      jumlahPenumpang: {
          dewasa: jumlahDewasa,
          anak: jumlahAnak,
          bayi: jumlahBayi,
          lansia: jumlahLansia,
          total: jumlahDewasa + jumlahAnak + jumlahBayi + jumlahLansia
      }
  };

  sessionStorage.setItem('bookingData', JSON.stringify(data));
  window.location.href = 'ticketdetail.html';
});
