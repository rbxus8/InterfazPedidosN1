// Este script carga los productos disponibles para un local seleccionado de forma sencilla
function cargarProductos(localId) {
    // Verifica que se haya seleccionado un local
    if (!localId) return;

    // Hace una petición al servidor para obtener los productos del local
    fetch('obtener_productos.php?local_id=' + localId)
        .then(function(response) {
            return response.json(); // Convierte la respuesta a JSON
        })
        .then(function(data) {
            var productosContainer = document.getElementById('productos-container');
            productosContainer.innerHTML = ''; // Limpia el contenedor

            if (data.success) {
                // Recorre los productos y los muestra en pantalla
                data.productos.forEach(function(producto) {
                    // Crea el HTML para cada producto
                    var productoHTML = 
                        '<div>' +
                            '<label>' +
                                '<input type="checkbox" name="productos[' + producto.id_producto + '][seleccionado]" value="1">' +
                                producto.nombre + ' (Stock disponible: ' + producto.stock_disponible + ')' +
                            '</label>' +
                            '<input type="number" name="productos[' + producto.id_producto + '][cantidad]" placeholder="Cantidad" min="1" max="' + producto.stock_disponible + '" required>' +
                        '</div>';
                    productosContainer.innerHTML += productoHTML;
                });
            } else {
                productosContainer.innerHTML = '<p>No hay productos disponibles para este local.</p>';
            }
        })
        .catch(function(error) {
            console.error('Error al cargar productos:', error);
        });
}



function cambiarColorTema() {
    const header = document.getElementsByClassName('header')[0]; //variable que almacena el header
    const chanceColorButton = document.getElementById('chance_color'); // variable que almacena el botón de cambio de color
    const container = document.getElementsByClassName('container')[0]; // variable que almacena el contenedor principal
    const footer = document.getElementsByClassName('footer')[0]; // variable que almacena el footer

    // Alterna el color de fondo del header entre blanco y azul claro
    if (header.style.backgroundColor === 'white' && 
        chanceColorButton.style.backgroundColor === 'red' &&
        container.style.backgroundColor === 'white' &&
        footer.style.backgroundColor === 'white') {

        header.style.backgroundColor = '#5f8fa0'; 
        chanceColorButton.style.backgroundColor = 'white';
        container.style.backgroundColor = '#5f8fa0f0'; // Cambia el color del contenedor principal
        footer.style.backgroundColor = '#5f8fa0'; // Cambia el color del footer

    } else {
        header.style.backgroundColor = 'white';
        chanceColorButton.style.backgroundColor = 'red';
        container.style.backgroundColor = 'white'; // Cambia el color del contenedor principal
        footer.style.backgroundColor = 'white'; // Cambia el color del footer
    }
}

 


    function habilitarCantidad(idProducto) {
        // Obtiene el input de cantidad para el producto seleccionado
        var cantidadInput = document.getElementById('cantidad_' + idProducto);

        // Si la casilla está marcada, habilita el campo de cantidad
        if (document.querySelector('input[name="nuevos_productos[' + idProducto + '][seleccionado]"]:checked')) {
            cantidadInput.disabled = false;
        } else {
            cantidadInput.disabled = true;
        }
    }

    // Función que habilita la cantidad y el botón de agregar
    function habilitarCantidad(idProducto) {
        var cantidadInput = document.getElementById('cantidad_' + idProducto);
        var botonAgregar = document.getElementById('agregar_' + idProducto);

        // Si la casilla está marcada, habilita el campo de cantidad
        if (document.querySelector('input[name="nuevos_productos[' + idProducto + '][seleccionado]"]:checked')) {
            cantidadInput.disabled = false;
        } else {
            cantidadInput.disabled = true;
            botonAgregar.disabled = true; // Deshabilita el botón si el producto no está seleccionado
        }

        // Si hay una cantidad válida, habilita el botón "Agregar"
        if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
            botonAgregar.disabled = false; // Habilita el botón "Agregar"
        } else {
            botonAgregar.disabled = true; // Deshabilita el botón si la cantidad es inválida
        }

        // Escucha el cambio en el input de cantidad
        cantidadInput.addEventListener('input', function() {
            if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
                botonAgregar.disabled = false;
            } else {
                botonAgregar.disabled = true;
            }
        });
    }

    // Función para agregar el producto (sin redirigir)
    function agregarProducto(idProducto) {
        var cantidadInput = document.getElementById('cantidad_' + idProducto);
        var cantidad = cantidadInput.value;

        if (cantidad > 0) {
            // Aquí debes hacer la lógica para agregar el producto a la base de datos sin redirigir
            var formData = new FormData();
            formData.append('id_producto', idProducto);
            formData.append('cantidad', cantidad);

            // Enviar los datos al servidor
            fetch('agregar_producto_pedido.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert('Producto agregado correctamente!');
                    // Actualizar la lista de productos o realizar alguna acción después de agregar el producto
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un problema al agregar el producto');
                });
        }
    }


            // Función para mostrar el número de celular del cliente seleccionado
        function mostrarTelefono() {
            const selectCliente = document.getElementById('cliente');
            const telefono = selectCliente.options[selectCliente.selectedIndex].dataset.telefono;
            document.getElementById('telefono').innerText = telefono ? `Teléfono: ${telefono}` : '';
        }

        function cargarProductos(idLocal) {
            fetch(`obtener_productos.php?local=${idLocal}`)
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('productos-container');
                    if (data.success) {
                        let html = '<ul>';
                        data.productos.forEach(producto => {
                            html += `
                                <li>
                                    <label>
                                        <input  type="checkbox" name="productos[${producto.id_producto}][seleccionado]" value="1">
                                        ${producto.nombre} (Stock: ${producto.cantidad_producto})
                                    </label>
                                    <input  type="number" name="productos[${producto.id_producto}][cantidad]" 
                                           placeholder="Cantidad" min="1" max="${producto.cantidad_producto}" disabled>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        contenedor.innerHTML = html;

                        // Habilitar el input de cantidad al seleccionar un producto
                        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const cantidadInput = this.closest('li').querySelector('input[type="number"]');
                                cantidadInput.disabled = !this.checked;
                            });
                        });
                    } else {
                        contenedor.innerHTML = `<p>${data.message}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los productos:', error);
                    document.getElementById('productos-container').innerHTML = `<p>Error al cargar los productos.</p>`;
                });
        }