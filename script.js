

// este script se encarga de cargar los productos disponibles para un local seleccionado
function cargarProductos(localId) {
    if (localId) {
        fetch('obtener_productos.php?local_id=' + localId)
            .then(response => response.json())
            .then(data => {
                let productosContainer = document.getElementById('productos-container');
                productosContainer.innerHTML = '';

                if (data.success) {
                    data.productos.forEach(producto => {
                        let productoHTML = `
                            <div>
                                <label>
                                    <input type="checkbox" name="productos[${producto.id_producto}][seleccionado]" value="1">
                                    ${producto.nombre} (Stock disponible: ${producto.stock_disponible})
                                </label>
                                <input type="number" name="productos[${producto.id_producto}][cantidad]" placeholder="Cantidad" min="1" max="${producto.stock_disponible}" required>
                            </div>
                        `;
                        productosContainer.innerHTML += productoHTML;
                    });
                } else {
                    productosContainer.innerHTML = '<p>No hay productos disponibles para este local.</p>';
                }
            })
            .catch(error => {
                console.error('Error al cargar productos:', error);
            });
    }
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

 
