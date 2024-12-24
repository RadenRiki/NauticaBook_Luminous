/** 
 * Cargo Payment System Component
 */
const { useState, useEffect } = React;

function CargoPaymentSystem() {
  const [paymentMethod, setPaymentMethod] = useState('');
  const [cargoDetails, setCargoDetails] = useState(null);
  const [totalPrice, setTotalPrice] = useState(0);
  const [virtualAccount, setVirtualAccount] = useState('');
  const [countdown, setCountdown] = useState(3600); // 1 hour in seconds
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    // Debug logging for sessionStorage
    console.log('Checking sessionStorage data...');
    console.log('cargoBookingData:', sessionStorage.getItem('cargoBookingData'));
    console.log('totalPrice:', sessionStorage.getItem('totalPrice'));

    // Get cargo booking data and total price from sessionStorage
    const cargoBookingData = JSON.parse(sessionStorage.getItem('cargoBookingData'));
    const totalPriceStr = sessionStorage.getItem('totalPrice');
    
    // Debug logging for parsed data
    console.log('Parsed cargoBookingData:', cargoBookingData);
    
    if (cargoBookingData && totalPriceStr) {
      setCargoDetails(cargoBookingData);
      setTotalPrice(parseInt(totalPriceStr));
      console.log('State updated with cargoDetails and totalPrice');
    } else {
      console.log('Warning: Missing cargoBookingData or totalPrice in sessionStorage');
    }

    // Generate virtual account number
    const va = '88' + Math.random().toString().slice(2, 14);
    setVirtualAccount(va);
    console.log('Generated virtual account:', va);

    // Set countdown timer (1 hour)
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

  // Format seconds to HH:MM:SS
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

  // Handle payment confirmation
  const handlePayment = async () => {
    try {
      // Log data sebelum dikirim
      console.log('Session check request...');
      const response = await fetch('check_session.php');
      console.log('Session response:', await response.clone().text());
      
      const sessionData = await response.json();
      console.log('Session data:', sessionData);

      if (!sessionData.user_id) {
        alert('Silakan login terlebih dahulu');
        window.location.href = 'login.html';
        return;
      }

      // Get cargo booking data from sessionStorage
      const cargoBookingData = JSON.parse(sessionStorage.getItem('cargoBookingData'));
      console.log('Retrieved cargoBookingData for payment:', cargoBookingData);
      
      // Debug original date
      console.log('Original date:', cargoBookingData.cargoDetails.tanggalPengiriman);

      // Prepare cargo data for saving
      const cargoData = {
        user_id: sessionData.user_id,
        asal: cargoBookingData.pengirim.kota,
        tujuan: cargoBookingData.penerima.kota,
        jenis: cargoBookingData.cargoDetails.jenisBarang,
        berat_kg: parseFloat(cargoBookingData.cargoDetails.beratBarang),
        tanggal: new Date(cargoBookingData.cargoDetails.tanggalPengiriman).toISOString().split('T')[0], // Format ke YYYY-MM-DD
        pengirim: cargoBookingData.pengirim,
        penerima: cargoBookingData.penerima,
        catatan: cargoBookingData.catatan || ''
      };

      // Debug logs
      console.log('Formatted date:', cargoData.tanggal);
      console.log('Final cargo data structure:', cargoData);

      // Send to save_cargo.php
      const saveResponse = await fetch('save_cargo.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(cargoData)
      });

      // Log response dari save_cargo.php
      console.log('Save response:', await saveResponse.clone().text());

      if (!saveResponse.ok) {
        throw new Error('Gagal menyimpan data cargo');
      }

      const result = await saveResponse.json();
      console.log('Parsed save response:', result);
      
      if (result.success) {
        window.location.href = 'payment_success.html';
      } else {
        throw new Error(result.error || 'Gagal menyimpan data cargo');
      }
      
    } catch (error) {
      console.error('Detailed error:', error);
      alert('Terjadi kesalahan saat memproses pembayaran: ' + error.message);
    }
  };

  return (
    <div className="w-full max-w-3xl mx-auto p-6">
      <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 className="text-2xl font-bold mb-4">Detail Pembayaran Cargo</h2>
        
        {cargoDetails && (
          <div className="mb-6">
            <p className="text-gray-600">
              Rute: {cargoDetails.pengirim.kota} → {cargoDetails.penerima.kota}
            </p>
            <p className="text-gray-600">
              Jenis Barang: {cargoDetails.cargoDetails.jenisBarang}
            </p>
            <p className="text-gray-600">
              Berat: {cargoDetails.cargoDetails.beratBarang} Kg
            </p>
            <p className="text-xl font-semibold mt-2">
              Total: Rp {totalPrice?.toLocaleString()}
            </p>
          </div>
        )}

        <div className="mb-6">
          <h3 className="text-lg font-semibold mb-2">Waktu Pembayaran Tersisa</h3>
          <div className="text-2xl font-mono bg-gray-100 p-4 rounded text-center">
            {formatTime(countdown)}
          </div>
        </div>

        <div className="space-y-4">
          {/* BANK TRANSFER OPTION */}
          <div
            className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
            onClick={() => setPaymentMethod('bank')}
          >
            <h3 className="font-semibold">Transfer Bank</h3>
            {paymentMethod === 'bank' && (
              <div className="mt-4 space-y-4">
                <div className="bg-gray-50 p-4 rounded-lg">
                  <p className="text-sm text-gray-600 mb-2">Nomor Virtual Account:</p>
                  <div className="flex items-center gap-2">
                    <code className="bg-white px-4 py-2 rounded flex-1">
                      {virtualAccount}
                    </code>
                    <button 
                      onClick={(e) => {
                        e.stopPropagation();
                        copyToClipboard(virtualAccount);
                      }}
                      className="p-2 hover:bg-gray-200 rounded"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                      </svg>
                    </button>
                  </div>
                </div>
                {copied && (
                  <div className="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <div className="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                      </svg>
                      <span className="ml-2">
                        Nomor Virtual Account berhasil disalin!
                      </span>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* MINIMARKET OPTION */}
          <div
            className="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
            onClick={() => setPaymentMethod('minimarket')}
          >
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

        {/* CONFIRMATION BUTTON */}
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
}

export default CargoPaymentSystem;