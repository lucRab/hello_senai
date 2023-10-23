function hide(btn_id) {
    var press = btn_id;

    if(press == 'btn_inicio') {
        window.location.href = "/documentation/inicio";
    } else if(press == 'btn_usuario') {
        window.location.href = "/documentation/usuario";
    } else if(press == 'btn_convite') {
        window.location.href = "/documentation/convite";
    } else if(press == 'btn_projeto') {
        window.location.href = "/documentation/projeto";
    }
}