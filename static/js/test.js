//calculate font size base on container's height
function calculateFontSize() { 
    var container = document.getElementById('container');
    var fontSize = container.clientHeight * 0.8;
    container.style.fontSize = fontSize + 'px';
}