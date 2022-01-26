$(document).ready(function(){
    
    Cufon.replace('div.divContacto', {fontFamily:'Istok Web',fontSize:'18px'});
    
    $('li a').hover(
        function(){
            $('.descripcion').html("Conozca Nuestros Servicios:<br/>" + $(this).html() + " - " + this.title);
            Cufon.replace('div.descripcion', {fontFamily:'Days One',fontSize:'20px'});
        },
        function(){
            $('.descripcion').html("");
        }
    );
    
    $.ajax({
         url : "cortina/cliente.txt",
        dataType: "text",
        success : function (data) {
                $("div.cliente").html(data);
                Cufon.replace('div.cliente', {fontFamily:'Days One',fontSize:'50px'}); 
                Cufon.replace('div.cliente span', {fontFamily:'Days One',fontSize:'65px'}); 
        }
    });

});