<template>
  <main class="main">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
      <!--<li class="breadcrumb-item"><a class="text-decoration-none" href="/">Escritorio</a></li>-->
  </ol>
  <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
          <div class="card-header">
              <i class="fa fa-align-justify"></i> LISTA DE PRODUCTOS SERVICIOS
          </div>
          <div class="card-body">
            <div>
              <p><strong>Última sincronización: {{ result.lastSync }}</strong></p>
              <pre>{{ result.data }}</pre>
            </div>            
           </div>
          </div>
      </div>
</main>
</template>

<script>
  export default {
      data (){
          return {
            result: {
              data: [],
              lastSync: null 
            }
          }
      },
      methods : {
        async obtenerActividades() {
          try {
            const response = await axios.get('/factura/sincronizarListaProductosServicios'); // Cambia la ruta
            this.result.data = response.data;
            
            this.result.lastSync = new Date().toLocaleString();
          } catch (error) {
            console.error('Error al obtener actividades', error);
          }
        },
      },
      mounted() {
        this.obtenerActividades();
      }
  }
</script>
<style>    
  .modal-content{
      width: 100% !important;
      position: absolute !important;
  }
  .mostrar{
      display: list-item !important;
      opacity: 1 !important;
      position: absolute !important;
      background-color: #3c29297a !important;
  }
  .div-error{
      display: flex;
      justify-content: center;
  }
  .text-error{
      color: red !important;
      font-weight: bold;
  }
</style>