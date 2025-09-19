
// Este script carga los productos disponibles para un local seleccionado de forma sencilla

/**
 * Función para cargar los productos de un local específico.
 * @param {number|string} localId - El ID del local seleccionado.
 */
function cargarProductos(localId) {
    // Verifica que se haya seleccionado un local (si no hay localId, sale de la función)
    if (!localId) return;

    // Hace una petición al servidor para obtener los productos del local seleccionado
    fetch('obtener_productos.php?local_id=' + localId)
        .then(function (response) {
            // Convierte la respuesta del servidor a formato JSON
            return response.json();
        })
        .then(function (data) {
            // Obtiene el contenedor donde se mostrarán los productos
            var productosContainer = document.getElementById('productos-container');
            // Limpia el contenido anterior del contenedor
            productosContainer.innerHTML = '';

            // Si la respuesta del servidor indica éxito (data.success es true)
            if (data.success) {
                // Recorre el array de productos recibido desde el servidor
                data.productos.forEach(function (producto) {
                    // Crea el HTML para mostrar cada producto con un checkbox y un input de cantidad
                    var productoHTML =
                        '<div>' +
                        // Checkbox para seleccionar el producto
                        '<label>' +
                        '<input type="checkbox" name="productos[' + producto.id_producto + '][seleccionado]" value="1">' +
                        // Muestra el nombre del producto y el stock disponible
                        producto.nombre + ' (Stock disponible: ' + producto.stock_disponible + ')' +
                        '</label>' +
                        // Input numérico para ingresar la cantidad deseada (con límites según el stock)
                        '<input type="number" name="productos[' + producto.id_producto + '][cantidad]" placeholder="Cantidad" min="1" max="' + producto.stock_disponible + '" required>' +
                        '</div>';
                    // Agrega el HTML del producto al contenedor
                    productosContainer.innerHTML += productoHTML;
                });
            } else {
                // Si no hay productos disponibles, muestra un mensaje al usuario
                productosContainer.innerHTML = '<p>No hay productos disponibles para este local.</p>';
            }
        })
        .catch(function (error) {
            // Si ocurre un error en la petición, lo muestra en la consola
            console.error('Error al cargar productos:', error);
        });
}



// Función para cambiar el color del tema de la página (header, botón, contenedor y footer)
function cambiarColorTema() {
    // Obtiene el elemento header
    const header = document.getElementsByClassName('header')[0];
    // Obtiene el botón para cambiar el color
    const chanceColorButton = document.getElementById('chance_color');
    // Obtiene el contenedor principal
    const container = document.getElementsByClassName('container')[0];
    // Obtiene el footer
    const footer = document.getElementsByClassName('footer')[0];

    // Verifica si el color actual es blanco (tema claro)
    if (
        header.style.backgroundColor === 'white' &&
        chanceColorButton.style.backgroundColor === 'red' &&
        container.style.backgroundColor === 'white' &&
        footer.style.backgroundColor === 'white'
    ) {
        // Cambia a tema azul (oscuro)
        header.style.backgroundColor = '#5f8fa0';
        chanceColorButton.style.backgroundColor = 'white';
        container.style.backgroundColor = '#5f8fa0f0'; // Cambia el color del contenedor principal
        footer.style.backgroundColor = '#5f8fa0'; // Cambia el color del footer
    } else {
        // Cambia a tema blanco (claro)
        header.style.backgroundColor = 'white';
        chanceColorButton.style.backgroundColor = 'red';
        container.style.backgroundColor = 'white'; // Cambia el color del contenedor principal
        footer.style.backgroundColor = 'white'; // Cambia el color del footer
    }
}



// Función para habilitar el campo de cantidad al seleccionar un producto
function habilitarCantidad(idProducto) {
    const cantidadInput = document.getElementById('cantidad_' + idProducto);
    const botonAgregar = document.getElementById('agregar_' + idProducto);
    const checkbox = document.querySelector(
        'input[name="productos[' + idProducto + '][seleccionado]"]:checked'
    );

    if (checkbox) {
        cantidadInput.disabled = false;
    } else {
        cantidadInput.disabled = true;
        botonAgregar.disabled = true;
        return;
    }

    cantidadInput.addEventListener('input', function () {
        if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
            botonAgregar.disabled = false;
        } else {
            botonAgregar.disabled = true;
        }
    });
}



// Función que habilita la cantidad y el botón de agregar
// Esta función se encarga de habilitar/deshabilitar el campo de cantidad y el botón "Agregar" 
// según si el producto está seleccionado y si la cantidad ingresada es válida.
function habilitarCantidad(idProducto) {
    // Obtiene el input de cantidad para el producto seleccionado
    var cantidadInput = document.getElementById('cantidad_' + idProducto);
    // Obtiene el botón de agregar para el producto seleccionado
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

    // Escucha el cambio en el input de cantidad para habilitar/deshabilitar el botón "Agregar"
    cantidadInput.addEventListener('input', function () {
        if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
            botonAgregar.disabled = false;
        } else {
            botonAgregar.disabled = true;
        }
    });
}


// Función para agregar el producto (sin redirigir)
/**
 * Agrega un producto al pedido sin redirigir la página.
 * @param {number|string} idProducto - El ID del producto a agregar.
 */
function agregarProducto(idProducto) {
    // Obtiene el input de cantidad para el producto seleccionado
    var cantidadInput = document.getElementById('cantidad_' + idProducto);
    // Obtiene la cantidad ingresada por el usuario
    var cantidad = cantidadInput.value;

    // Verifica que la cantidad sea mayor a 0
    if (cantidad > 0) {
        // Crea un objeto FormData para enviar los datos al servidor
        var formData = new FormData();
        formData.append('id_producto', idProducto);
        formData.append('cantidad', cantidad);

        // Realiza una petición fetch para enviar los datos al servidor por POST
        fetch('agregar_producto_pedido.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // Convierte la respuesta a JSON
            .then(data => {
                // Muestra un mensaje de éxito al usuario
                alert('Producto agregado correctamente!');
                // Aquí puedes actualizar la lista de productos o realizar otra acción si es necesario
            })
            .catch(error => {
                // Si ocurre un error, lo muestra en la consola y alerta al usuario
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
/**
 * Función para cargar los productos de un local específico (versión alternativa).
 * @param {number|string} idLocal - El ID del local seleccionado.
 */
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
                            <input type="checkbox" 
                                   name="productos[${producto.id_producto}][seleccionado]" 
                                   value="1"
                                   onchange="habilitarCantidad(${producto.id_producto})">
                            ${producto.nombre} (Stock: ${producto.cantidad_producto})
                        </label>
                        <input type="number" 
                               id="cantidad_${producto.id_producto}" 
                               name="productos[${producto.id_producto}][cantidad]" 
                               placeholder="Cantidad" 
                               min="1" 
                               max="${producto.cantidad_producto}" 
                               disabled>
                        <button type="button" 
                                id="agregar_${producto.id_producto}" 
                                onclick="agregarProducto(${producto.id_producto})" 
                                disabled>Agregar</button>
                    </li>`;
                });
                html += '</ul>';
                contenedor.innerHTML = html;
            } else {
                contenedor.innerHTML = `<p>${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error al cargar los productos:', error);
            document.getElementById('productos-container').innerHTML =
                `<p>Error al cargar los productos.</p>`;
        });
}



// Función para mostrar los productos según la categoría
// Este bloque de código espera a que el DOM esté completamente cargado antes de ejecutarse
document.addEventListener('DOMContentLoaded', function () {
    /**
     * Muestra u oculta las tarjetas de productos según la categoría seleccionada.
     * @param {string} categoria - La categoría a mostrar ('todos' para mostrar todos).
     */
    function mostrar(categoria) {
        // Selecciona todas las tarjetas de productos dentro del contenedor .productos_cards
        const cards = document.querySelectorAll('.productos_cards .card');
        // Itera sobre cada tarjeta de producto
        cards.forEach(card => {
            if (categoria === 'todos') {
                // Si la categoría es 'todos', muestra todas las tarjetas
                card.style.display = 'block';
            } else if (card.classList.contains(categoria)) {
                // Si la tarjeta pertenece a la categoría seleccionada, la muestra
                card.style.display = 'block';
            } else {
                // Si no pertenece a la categoría, la oculta
                card.style.display = 'none';
            }
        });
    }
    // Al cargar la página, muestra todos los productos por defecto
    mostrar('todos');
    // Asigna eventos de click a los botones de categoría
    document.querySelectorAll('.categoria_productos .btn').forEach(btn => {
        btn.addEventListener('click', function () {
            // Obtiene la categoría desde el atributo onclick del botón usando una expresión regular
            const categoria = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            // Llama a la función mostrar con la categoría seleccionada
            mostrar(categoria);
        });
    });
});
