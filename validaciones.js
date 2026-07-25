function validarProducto() {
    const nombre = document.getElementById("nombre_producto").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const precio = Number(document.getElementById("precio").value);
    const stock = Number(document.getElementById("stock").value);

    if (nombre === "" || descripcion === "") {
        alert("Debe completar el nombre y la descripción del producto.");
        return false;
    }

    if (precio <= 0) {
        alert("El precio del producto debe ser mayor que cero.");
        return false;
    }

    if (stock < 0) {
        alert("El stock del producto no puede ser negativo.");
        return false;
    }

    return true;
}

function validarCliente() {
    const nombre = document.getElementById("nombre_cliente").value.trim();
    const email = document.getElementById("email").value.trim();
    const direccion = document.getElementById("direccion").value.trim();

    const formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (nombre === "" || email === "" || direccion === "") {
        alert("Debe completar todos los datos del cliente.");
        return false;
    }

    if (!formatoEmail.test(email)) {
        alert("Debe ingresar un correo electrónico válido.");
        return false;
    }

    return true;
}

function validarCompra() {
    const producto = document.getElementById("id_producto");
    const cliente = document.getElementById("id_cliente");
    const cantidad = Number(document.getElementById("cantidad").value);

    if (producto.value === "" || cliente.value === "") {
        alert("Debe seleccionar un producto y un cliente.");
        return false;
    }

    if (cantidad <= 0) {
        alert("La cantidad debe ser mayor que cero.");
        return false;
    }

    const opcionSeleccionada = producto.options[producto.selectedIndex];
    const stockDisponible = Number(opcionSeleccionada.dataset.stock);

    if (cantidad > stockDisponible) {
        alert("La cantidad solicitada supera el stock disponible.");
        return false;
    }

    return true;
}