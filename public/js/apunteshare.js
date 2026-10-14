document.addEventListener('DOMContentLoaded', () => {
 document.querySelectorAll('.demo-form').forEach(form => form.addEventListener('submit', event => {event.preventDefault(); if(form.reportValidity()) alert('Esta pantalla es una demostración visual. La funcionalidad se conectará posteriormente con Laravel.');}));
 document.querySelectorAll('.demo-action').forEach(button => button.addEventListener('click', () => alert('Acción de demostración: pendiente de conexión al backend.')));
});
