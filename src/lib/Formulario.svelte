<script lang="ts">

  interface FormData {
    estudiante: string;
    docente: string;
    asignatura: string;
    grado: string;
    periodo: string;
    valoracion: string;
    year: string;
  }

  const formData: FormData = {
    estudiante: "",
    docente: "",
    asignatura: "",
    grado: "",
    periodo: "",
    valoracion: "",
    year: new Date().getFullYear().toString(), // Valor por defecto del campo "year" es el año actual
  };

  function handleSubmit() {
    // Lógica para manejar el envío del formulario
  }
</script>
<main class="container">
<form class="row g-3" on:submit|preventDefault={handleSubmit}>
  <input type="hidden" name="currentYear" value={formData.year} />
  <!-- Campo oculto con el año actual -->
  <div class="col-md-6">
    <label for="grado" class="form-label">Grado:</label>
    <input
      type="text"
      id="grado"
      class="form-control"
      bind:value={formData.grado}
    />
  </div>
  <div class="col-md-6">
    <label for="estudiante" class="form-label">Estudiante:</label>
    <input
      type="text"
      id="estudiante"
      class="form-control"
      bind:value={formData.estudiante}
    />
  </div>

  <div class="col-md-6">
    <label for="docente" class="form-label">Docente:</label>
    <input
      type="text"
      id="docente"
      class="form-control"
      bind:value={formData.docente}
    />
  </div>

  <div class="col-md-6">
    <label for="asignatura" class="form-label">Asignatura:</label>
    <input
      type="text"
      id="asignatura"
      class="form-control"
      bind:value={formData.asignatura}
    />
  </div>

  

  <div class="col-md-6">
    <label for="periodo" class="form-label">Periodo:</label>
    <input
      type="text"
      id="periodo"
      class="form-control"
      bind:value={formData.periodo}
    />
  </div>

  <div class="col-md-6">
    <label for="valoracion" class="form-label">Valoración:</label>
    <input
      type="text"
      id="valoracion"
      class="form-control"
      bind:value={formData.valoracion}
    />
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-primary">Enviar</button>
  </div>
</form>
</main>