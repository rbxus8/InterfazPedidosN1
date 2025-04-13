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
