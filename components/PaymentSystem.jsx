import React, { useState, useEffect } from 'react';

const PaymentSystem = () => {
  const [paymentMethod, setPaymentMethod] = useState('');
  const [bookingData, setBookingData] = useState(null);
  const [totalPrice, setTotalPrice] = useState(0);
  const [virtualAccount, setVirtualAccount] = useState('');
  const [countdown, setCountdown] = useState(3600); // 1 hour in seconds
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    // Ambil data booking dan total price dari sessionStorage
    const bookingDataStr = sessionStorage.getItem('bookingData');
    const totalPriceStr = sessionStorage.getItem('totalPrice');
    
    if (bookingDataStr && totalPriceStr) {
      setBookingData(JSON.parse(bookingDataStr));
      setTotalPrice(parseInt(totalPriceStr));
    }

    // Generate virtual account number
    const va = '88' + Math.random().toString().slice(2, 14);
    setVirtualAccount(va);

    // Set countdown timer
    const timer = setInterval(() => {
      setCountdown(prev => {
        if (prev <= 0) {
          clearInterval(timer);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, []);

  const formatTime = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handlePayment = async () => {
    try {
      // Ambil user_id dan data booking
      const userData = JSON.parse(sessionStorage.getItem('user'));
      const bookingData = JSON.parse(sessionStorage.getItem('bookingData'));
      const pemesanData = JSON.parse(sessionStorage.getItem('pemesanData'));
      
      if (!userData?.id) {
        alert('Silakan login terlebih dahulu');
        window.location.href = 'login.html';
        return;
      }
  
      // Data untuk dikirim ke database
      const ticketData = {
        user_id: userData.id,
        asal: bookingData.pelabuhanAsal,
        tujuan: bookingData.pelabuhanTujuan,
        layanan: bookingData.layanan,
        tipe: bookingData.tipeTiket,
        jumlah_penumpang: bookingData.jumlahPenumpang.total,
        tanggal: bookingData.tanggal,
        jam: bookingData.jamMasuk,
        nama_pemesan: pemesanData.nama,
        email_pemesan: pemesanData.email,
        nomor_hp: pemesanData.telepon
      };
  
      // Kirim ke API untuk disimpan di database
      const response = await fetch('save_ticket.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(ticketData)
      });
  
      if (!response.ok) throw new Error('Gagal menyimpan tiket');
  
      // Jika berhasil
      sessionStorage.setItem('paymentStatus', 'success');
      window.location.href = 'payment-success.html';
    } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat memproses pembayaran');
    }
  };

  return (
    <div className="w-full max-w-3xl mx-auto p-6">
      <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 className="text-2xl font-bold mb-4">Detail Pembayaran</h2>
        
        {bookingData && (
          <div className="mb-6">
            <p className="text-gray-600">Rute: {bookingData.pelabuhanAsal} → {bookingData.pelabuhanTujuan}</p>
            <p className="text-gray-600">Tanggal: {new Date(bookingData.tanggal).toLocaleDateString('id-ID')}</p>
            <p className="text-gray-600">Layanan: {bookingData.layanan}</p>
            <p className="text-xl font-semibold mt-2">Total: Rp {totalPrice?.toLocaleString()}</p>
          </div>
        )}

        <div className="mb-6">
          <h3 className="text-lg font-semibold mb-2">Waktu Pembayaran Tersisa</h3>
          <div className="text-2xl font-mono bg-gray-100 p-4 rounded text-center">
            {formatTime(countdown)}
          </div>
        </div>

        <div className="space-y-4">
          <div className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
               onClick={() => setPaymentMethod('bank')}>
            <h3 className="font-semibold">Transfer Bank</h3>
            {paymentMethod === 'bank' && (
              <div className="mt-4 space-y-4">
                <div className="bg-gray-50 p-4 rounded-lg">
                  <p className="text-sm text-gray-600 mb-2">Nomor Virtual Account:</p>
                  <div className="flex items-center gap-2">
                    <code className="bg-white px-4 py-2 rounded flex-1">{virtualAccount}</code>
                    <button 
                      onClick={(e) => {
                        e.stopPropagation();
                        copyToClipboard(virtualAccount);
                      }}
                      className="p-2 hover:bg-gray-200 rounded"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                {copied && (
                  <div className="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <div className="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                      </svg>
                      <span className="ml-2">Nomor Virtual Account berhasil disalin!</span>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>

          <div className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
               onClick={() => setPaymentMethod('minimarket')}>
            <h3 className="font-semibold">Pembayaran Minimarket</h3>
            {paymentMethod === 'minimarket' && (
              <div className="mt-4 space-y-2">
                <p className="text-gray-600">Kode Pembayaran:</p>
                <div className="bg-gray-50 p-4 rounded-lg">
                  <code className="text-lg">{virtualAccount}</code>
                </div>
              </div>
            )}
          </div>
        </div>

        {paymentMethod && (
          <button
            onClick={handlePayment}
            className="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700"
          >
            Konfirmasi Pembayaran
          </button>
        )}
      </div>
    </div>
  );
};

export default PaymentSystem;