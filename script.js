let isDropdownOpen = false;

function togglePassengerDropdown() {
  const dropdown = document.querySelector('.passenger-dropdown');
  isDropdownOpen = !isDropdownOpen;
  dropdown.classList.toggle('active', isDropdownOpen);
}

function increaseCount(type) {
  const countElement = document.getElementById(`${type}-count`);
  let count = parseInt(countElement.textContent);
  countElement.textContent = count + 1;
}

function decreaseCount(type) {
  const countElement = document.getElementById(`${type}-count`);
  let count = parseInt(countElement.textContent);
  if (count > 0) {
    countElement.textContent = count - 1;
  }
}

function savePassengers() {
  const dewasa = document.getElementById('dewasa-count').textContent;
  const anak = document.getElementById('anak-count').textContent;
  const bayi = document.getElementById('bayi-count').textContent;
  const lansia = document.getElementById('lansia-count').textContent;
  
  // Tutup dropdown setelah menyimpan
  togglePassengerDropdown();
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

function saveTicketType() {
  const selectedTicket = document.querySelector('input[name="ticket-type"]:checked');
  if (selectedTicket) {
    // Simpan nilai yang dipilih
    const ticketValue = selectedTicket.value;
    // Tutup dropdown
    toggleTicketDropdown();
  }
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
    formPenumpang.style.display = 'block';
    formCargo.style.display = 'none';
    btnPenumpang.classList.add('active');
    btnCargo.classList.remove('active');
  } else if (type === 'cargo') {
    formPenumpang.style.display = 'none';
    formCargo.style.display = 'block';
    btnPenumpang.classList.remove('active');
    btnCargo.classList.add('active');
  }
}

// Tambahkan handler submit untuk formCargo
document.getElementById('formCargo').addEventListener('submit', (event) => {
  event.preventDefault(); // Mencegah refresh halaman
  alert('Form Cargo berhasil dikirim!');
});