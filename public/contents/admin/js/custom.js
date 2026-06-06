setTimeout(function(){
	$('.alert_success').slideUp(100);
},2000);


setTimeout(function(){
	$('.alert_error').slideUp(100);
},2000);

setTimeout(() => {
	const alert = document.getElementById('success-alert');
	if(alert){
		alert.style.transition = 'opacity 0.5s';
		alert.style.opacity = '0';
		setTimeout(() => {alert.remove(), 500});
	}
}, 3000);

$(document).ready( function () {
    $('#myTable').DataTable();
} );
