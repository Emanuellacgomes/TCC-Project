<?php
session_start();
require_once "db_connection.php";

$total_price = isset($_SESSION['total_carrinho']) ? $_SESSION['total_carrinho'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="logo.png" alt="Logo da Playtopia" class="logo-loja">
    <a href="carrinho.php" class="back-to-shop-btn-cart">Voltar para o Carrinho</a>
</div>

<div class="checkout-container">
    <h2>Finalizar Compra</h2>
    
    <!-- Passo 1 - Forma de Pagamento -->
    <div class="checkout-step" id="step-1">
        <h3><span class="step-number">1</span> - Forma de Pagamento</h3>
        <div class="order-summary">
            <p>Total a pagar: <span class="total-price-display">R$ <?php echo number_format($total_price, 2, ',', '.'); ?></span></p>
        </div>
        
        <div class="payment-method">
            <div class="payment-options">
                <div class="payment-option selected">
                    <img src="mercadopago-logo.png" 
                         alt="Mercado Pago" style="width:80px;">
                    <span>Mercado Pago (Cartão de Crédito, Saldo na conta)</span>
                </div>
            </div>

            <div class="payment-form active-form">
                <button type="button" class="submit-payment-btn" onclick="goToStep(2, 'mercadopago')">
                    Continuar com Mercado Pago
                </button>
            </div>
        </div>
    </div>

    <!-- Passo 2 - Endereço de Entrega -->
    <div class="checkout-step" id="step-2" style="display:none;">
        <h3><span class="step-number">2</span> - Endereço de Entrega</h3>
        <p>Selecione uma das opções abaixo para definir o endereço.</p>
        <div class="address-options">
            <button type="button" id="btn-manual" onclick="showAddressForm('manual')">Inserir Manualmente</button>
            <button type="button" id="btn-geolocation" onclick="showAddressForm('geolocation')">Usar Localização do Dispositivo</button>
            <button type="button" id="btn-map" onclick="showAddressForm('map')">Selecionar no Mapa</button>
        </div>

        <form id="address_form" onsubmit="return goToStep(3)">
        <div id="manual_form" style="display:none;">
    <label for="cep">CEP:</label>
    <div style="display:flex; align-items:center; gap:8px;">
        <input 
            type="text" 
            id="cep" 
            name="cep" 
            placeholder="00000-000" 
            maxlength="9" 
            onblur="buscarCEP()" 
            style="flex:1;"
            value="<?php echo isset($_SESSION['cep_usuario']) ? htmlspecialchars($_SESSION['cep_usuario']) : ''; ?>">
        <div id="cep-loading" style="display:none;">
            <span style="font-size:12px; color:#555;">🔄 Buscando...</span>
        </div>
    </div>

    <label for="endereco">Endereço:</label>
    <input type="text" id="endereco" name="endereco" placeholder="Rua, Av." 
           value="<?php echo isset($_SESSION['endereco_usuario']) ? htmlspecialchars($_SESSION['endereco_usuario']) : ''; ?>">

    <label for="bairro">Bairro:</label>
    <input type="text" id="bairro" name="bairro" placeholder="Bairro" 
           value="<?php echo isset($_SESSION['bairro_usuario']) ? htmlspecialchars($_SESSION['bairro_usuario']) : ''; ?>">

    <label for="cidade">Cidade:</label>
    <input type="text" id="cidade" name="cidade" placeholder="Cidade" 
           value="<?php echo isset($_SESSION['cidade_usuario']) ? htmlspecialchars($_SESSION['cidade_usuario']) : ''; ?>">

    <label for="estado">Estado:</label>
    <input type="text" id="estado" name="estado" placeholder="Estado" 
           value="<?php echo isset($_SESSION['estado_usuario']) ? htmlspecialchars($_SESSION['estado_usuario']) : ''; ?>">

    <div id="cep-status" style="margin-top:6px; font-size:13px; color:#555;"></div>
</div>

<script>
function buscarCEP() {
    const cepInput = document.getElementById('cep');
    const cep = cepInput.value.replace(/\D/g, '');
    const statusDiv = document.getElementById('cep-status');
    const loading = document.getElementById('cep-loading');

    if (cep.length !== 8) {
        statusDiv.textContent = "❌ CEP inválido. Digite 8 números.";
        return;
    }

    loading.style.display = "inline";
    statusDiv.textContent = "";

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = "none";

            if (!data.erro) {
                document.getElementById('endereco').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('cidade').value = data.localidade || '';
                document.getElementById('estado').value = data.uf || '';

                // Bloqueia cidade e estado apenas se CEP foi digitado
                document.getElementById('cidade').readOnly = true;
                document.getElementById('estado').readOnly = true;
                document.getElementById('cidade').style.background = '#f5f5f5';
                document.getElementById('estado').style.background = '#f5f5f5';

                statusDiv.innerHTML = "✅ Endereço carregado automaticamente.";
                statusDiv.style.color = "green";
            } else {
                statusDiv.innerHTML = "❌ CEP não encontrado.";
                statusDiv.style.color = "red";
            }
        })
        .catch(() => {
            loading.style.display = "none";
            statusDiv.innerHTML = "⚠️ Erro ao buscar CEP. Tente novamente.";
            statusDiv.style.color = "orange";
        });
}

// Se já houver CEP vindo da sessão, bloqueia os campos cidade e estado
window.addEventListener("DOMContentLoaded", function() {
    const cep = document.getElementById('cep').value;
    const cidade = document.getElementById('cidade');
    const estado = document.getElementById('estado');

    if (cep && cep.trim() !== "") {
        cidade.readOnly = true;
        estado.readOnly = true;
        cidade.style.background = '#f5f5f5';
        estado.style.background = '#f5f5f5';
    }
});
</script>

            
            <div id="geolocation_info" style="display:none;">
                <p>Aguardando sua permissão para usar a localização...</p>
                <p id="geolocation-status"></p>
                <p>Latitude: <span id="lat-display"></span></p>
                <p>Longitude: <span id="lng-display"></span></p>
            </div>
            
            <div id="map_container" style="display:none;">
                <p>Clique e arraste o marcador para selecionar a localização exata.</p>
                <div id="map"></div>
                <input type="hidden" id="map_lat" name="map_latitude">
                <input type="hidden" id="map_lng" name="map_longitude">
            </div>
            <button type="submit" class="submit-payment-btn">Continuar</button>
        </form>
    </div>
    
    <!-- Passo 3 - Confirmação -->
    <div class="checkout-step" id="step-3" style="display:none;">
        <h3><span class="step-number">3</span> - Confirmação do Pedido</h3>
        <div class="summary-box">
            <p>Forma de Pagamento: <strong id="summary_payment_method"></strong></p>
            <p>Endereço de Entrega: <strong id="summary_address"></strong></p>
            <p>Total: <strong id="summary_total"></strong></p>
            <button type="button" class="submit-payment-btn" onclick="submitCheckout()">Confirmar e Finalizar</button>
        </div>
    </div>
    
    <!-- Formulário Hidden que vai para processar_pagamento.php -->
    <form id="checkout_form" action="processar_pagamento.php" method="POST">
        <input type="hidden" id="payment_method_input" name="payment_method" value="mercadopago">
        <input type="hidden" id="total_price_input" name="total_price" value="<?php echo isset($_SESSION['total_carrinho']) ? $_SESSION['total_carrinho'] : $total_price; ?>">
        <div id="address_inputs_container"></div>
    </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    let selectedPaymentMethod = 'mercadopago'; // fixo agora

    function goToStep(step, paymentMethod = null) {
        if (paymentMethod) {
            document.getElementById('payment_method_input').value = paymentMethod;
        }

        if (step === 2 && !selectedPaymentMethod) {
            alert('Por favor, selecione a forma de pagamento.');
            return false;
        }

        if (step === 3) {
            const paymentMethodText = {
                'mercadopago': 'Mercado Pago (Pix, Cartão, QR Code)'
            };
            document.getElementById('summary_payment_method').textContent = paymentMethodText[selectedPaymentMethod];
            document.getElementById('summary_total').textContent = 'R$ <?php echo number_format($total_price, 2, ',', '.'); ?>';

            const addressInputs = document.getElementById('address_inputs_container');
            addressInputs.innerHTML = '';
            
            const selectedAddressType = document.querySelector('.address-options button.selected');
            let addressText = '';

            if (selectedAddressType && selectedAddressType.id === 'btn-manual') {
                addressInputs.innerHTML += `<input type="hidden" name="cep" value="${document.getElementById('cep').value}">`;
                addressInputs.innerHTML += `<input type="hidden" name="endereco" value="${document.getElementById('endereco').value}">`;
                addressInputs.innerHTML += `<input type="hidden" name="cidade" value="${document.getElementById('cidade').value}">`;
                addressInputs.innerHTML += `<input type="hidden" name="estado" value="${document.getElementById('estado').value}">`;
                addressText = `${document.getElementById('endereco').value}, ${document.getElementById('cidade').value} - ${document.getElementById('estado').value}`;
            } else if (selectedAddressType && selectedAddressType.id === 'btn-geolocation') {
                addressInputs.innerHTML += `<input type="hidden" name="latitude" value="${document.getElementById('lat-display').textContent}">`;
                addressInputs.innerHTML += `<input type="hidden" name="longitude" value="${document.getElementById('lng-display').textContent}">`;
                addressText = `Coordenadas: ${document.getElementById('lat-display').textContent}, ${document.getElementById('lng-display').textContent}`;
            } else if (selectedAddressType && selectedAddressType.id === 'btn-map') {
                addressInputs.innerHTML += `<input type="hidden" name="map_latitude" value="${document.getElementById('map_lat').value}">`;
                addressInputs.innerHTML += `<input type="hidden" name="map_longitude" value="${document.getElementById('map_lng').value}">`;
                addressText = `Coordenadas do Mapa: ${document.getElementById('map_lat').value}, ${document.getElementById('map_lng').value}`;
            }

            document.getElementById('summary_address').textContent = addressText;

            document.querySelectorAll('.checkout-step').forEach(stepDiv => stepDiv.style.display = 'none');
            document.getElementById('step-3').style.display = 'block';

        } else {
            document.querySelectorAll('.checkout-step').forEach(stepDiv => stepDiv.style.display = 'none');
            document.getElementById('step-' + step).style.display = 'block';
        }
        
        return false;
    }

    function showAddressForm(formType) {
        document.querySelectorAll('.address-options button').forEach(btn => btn.classList.remove('selected'));
        document.getElementById(`btn-${formType}`).classList.add('selected');

        document.getElementById('manual_form').style.display = 'none';
        document.getElementById('geolocation_info').style.display = 'none';
        document.getElementById('map_container').style.display = 'none';

        if (formType === 'manual') {
            document.getElementById('manual_form').style.display = 'block';
        } else if (formType === 'geolocation') {
            document.getElementById('geolocation_info').style.display = 'block';
            getLocation();
        } else if (formType === 'map') {
            document.getElementById('map_container').style.display = 'block';
            initMap();
        }
    }
    
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            document.getElementById('geolocation-status').textContent = "Geolocalização não é suportada por este navegador.";
        }
    }

    function showPosition(position) {
        document.getElementById('lat-display').textContent = position.coords.latitude;
        document.getElementById('lng-display').textContent = position.coords.longitude;
        document.getElementById('geolocation-status').textContent = "Localização obtida com sucesso!";
    }

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                document.getElementById('geolocation-status').textContent = "Usuário negou a solicitação de Geolocalização.";
                break;
            case error.POSITION_UNAVAILABLE:
                document.getElementById('geolocation-status').textContent = "Localização indisponível.";
                break;
            case error.TIMEOUT:
                document.getElementById('geolocation-status').textContent = "A solicitação para obter a localização expirou.";
                break;
            case error.UNKNOWN_ERROR:
                document.getElementById('geolocation-status').textContent = "Ocorreu um erro desconhecido.";
                break;
        }
    }
    
    function initMap() {
        const saoPaulo = [-23.5505, -46.6333]; 
        
        if (map) {
            map.setView(saoPaulo, 12);
            marker.setLatLng(saoPaulo);
        } else {
            map = L.map('map').setView(saoPaulo, 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker(saoPaulo, { draggable: true }).addTo(map)
                .bindPopup("Arraste para selecionar a localização de entrega.")
                .openPopup();
                
            marker.on('dragend', function(e) {
                const newCoords = e.target.getLatLng();
                document.getElementById('map_lat').value = newCoords.lat;
                document.getElementById('map_lng').value = newCoords.lng;
            });
        }

        document.getElementById('map_lat').value = saoPaulo[0];
        document.getElementById('map_lng').value = saoPaulo[1];
    }
    
    function submitCheckout() {
        const form = document.getElementById('checkout_form');
        form.submit();
    }
</script>
</body>
</html>
