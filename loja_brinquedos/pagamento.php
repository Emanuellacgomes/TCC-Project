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
    <img src="icone.png" alt="Logo da Playtopia" class="logo-loja">
    <a href="carrinho.php" class="back-to-shop-btn">Voltar para o Carrinho</a>
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
                    <img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/ui-library/mercado-pago.svg" 
                         alt="Mercado Pago" style="width:80px;">
                    <span>Mercado Pago (Pix, Cartão, QR Code)</span>
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
                <input type="text" id="cep" name="cep" placeholder="00000-000">
                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" placeholder="Rua, Av.">
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade" placeholder="Cidade">
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" placeholder="Estado">
            </div>
            
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
        <input type="hidden" id="total_price_input" name="total_price" value="<?php echo htmlspecialchars(str_replace('.', ',', $total_price)); ?>">
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
