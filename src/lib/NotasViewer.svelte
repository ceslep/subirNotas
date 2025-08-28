<script>
  import { createEventDispatcher } from "svelte";
  import ConsolidatedInfoViewer from './ConsolidatedInfoViewer.svelte';

  export let notes = [];

  const dispatch = createEventDispatcher();

  let filterAsignatura = "";
  let filterEstudiante = "";
  let filterNombreEstudiante = ""; // New reactive variable for student name filter
  let globalSearchQuery = ""; // New reactive variable for global search

  // Default columns to show (assuming 'aspectos' refers to these)
  const defaultAspectsColumns = ['nombreEstudiante', 'asignatura', 'valoracion', 'estudiante', 'periodo', 'aspecto1', 'aspecto2', 'aspecto3', 'aspecto4', 'aspecto5', 'aspecto6', 'aspecto7', 'aspecto8', 'aspecto9', 'aspecto10', 'aspecto11', 'aspecto12', 'nota1', 'nota2', 'nota3', 'nota4', 'nota5', 'nota6', 'nota7', 'nota8', 'nota9', 'nota10', 'nota11', 'nota12'];
  let selectedColumns = [...defaultAspectsColumns];

  // Define all columns that should be displayable as options
  const displayableColumns = [
    'nombreEstudiante', // Place nombreEstudiante at the beginning
    'asignatura',
    'valoracion',
    'estudiante',
    'periodo',
    'aspecto1', 'aspecto2', 'aspecto3', 'aspecto4', 'aspecto5', 'aspecto6',
    'aspecto7', 'aspecto8', 'aspecto9', 'aspecto10', 'aspecto11', 'aspecto12',
    'nota1', 'nota2', 'nota3', 'nota4', 'nota5', 'nota6',
    'nota7', 'nota8', 'nota9', 'nota10', 'nota11', 'nota12'
  ];

  function closeViewer() {
    dispatch("close");
  }

  // Extract unique values for dropdowns
  $: uniqueAsignaturas = [...new Set(notes.map((note) => note.asignatura))].sort();
  $: uniqueEstudiantes = [...new Set(notes.map((note) => String(note.estudiante)))].sort();
  $: uniqueNombresEstudiante = [...new Set(notes.map((note) => note.nombreEstudiante))].sort(); // New unique names extraction

  // Reactive filtered notes array
  $: filteredNotes = notes.filter((note) => {
    const matchesAsignatura = filterAsignatura === "" || note.asignatura === filterAsignatura;
    const matchesEstudiante = filterEstudiante === "" || String(note.estudiante) === filterEstudiante;
    const matchesNombreEstudiante = filterNombreEstudiante === "" || note.nombreEstudiante === filterNombreEstudiante; // New filter condition

    // Global search logic
    const matchesGlobalSearch = globalSearchQuery === "" ||
      Object.values(note).some(value =>
        String(value).toLowerCase().includes(globalSearchQuery.toLowerCase())
      );

    return matchesAsignatura && matchesEstudiante && matchesNombreEstudiante && matchesGlobalSearch;
  });

  // All possible headers from the notes data, filtered by displayableColumns
  let allColumns = [];
  $: if (notes.length > 0) {
    allColumns = Object.keys(notes[0]).filter(column => displayableColumns.includes(column));
  } else {
    allColumns = [];
  }

  let columnsWithAllNulls = new Set();

  $: { // This block will react to changes in filteredNotes
    if (filteredNotes.length > 0 && allColumns.length > 0) { // Ensure allColumns is populated
      columnsWithAllNulls = new Set();
      for (const column of allColumns) { // Iterate over all possible columns
        let allNull = true;
        for (const note of filteredNotes) { // <-- Now uses 'filteredNotes'
          if (note[column] !== null) {
            allNull = false;
            break;
          }
        }
        if (allNull) {
          columnsWithAllNulls.add(column);
        }
      }
    } else {
      columnsWithAllNulls = new Set();
    }

    // Filter selectedColumns to remove any columns that are now all null based on filtered data
    selectedColumns = selectedColumns.filter(col => !columnsWithAllNulls.has(col));
  }

  // Function to toggle column selection
  function toggleColumn(column) {
    const index = selectedColumns.indexOf(column);
    if (index > -1) {
      selectedColumns.splice(index, 1);
    } else {
      selectedColumns.push(column);
    }
    selectedColumns = selectedColumns; // Trigger reactivity
  }

  // Reactive variable to check if all *visible* columns are selected
  $: visibleColumnsForSelection = allColumns.filter(col => !columnsWithAllNulls.has(col));
  $: allVisibleColumnsSelected = selectedColumns.length === visibleColumnsForSelection.length && visibleColumnsForSelection.length > 0;

  // Function to toggle all *visible* columns
  function toggleAllColumns() {
    if (allVisibleColumnsSelected) {
      selectedColumns = []; // Deselect all
    } else {
      selectedColumns = [...visibleColumnsForSelection]; // Select all visible
    }
  }

  let showConsolidatedViewer = false;
  let selectedNoteForConsolidation = {};

  function showConsolidatedInfo(note) {
    selectedNoteForConsolidation = note;
    showConsolidatedViewer = true;
  }
</script>

<div class="modal-overlay" on:click={(e) => e.target === e.currentTarget && closeViewer()} on:keydown={(e) => e.key === 'Escape' && closeViewer()} tabindex="-1">
  <div class="modal-content" on:click|stopPropagation on:keydown={(e) => e.key === 'Escape' && closeViewer()}>
    <div class="bg-success text-white p-2 mb-3">
      <h2 class="mb-0 text-light bg-success">Notas Completas</h2>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0">Filtros y Búsqueda</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="globalSearch" class="form-label">Buscar en toda la tabla:</label>
            <input
              type="text"
              id="globalSearch"
              class="form-control"
              bind:value={globalSearchQuery}
              placeholder="Buscar..."
            />
          </div>
          <div class="col-md-6 mb-3">
            <label for="filterAsignatura" class="form-label">Filtrar por Asignatura:</label>
            <select
              id="filterAsignatura"
              class="form-select"
              bind:value={filterAsignatura}
            >
              <option value="">Todas</option>
              {#each uniqueAsignaturas as asignatura}
                <option value={asignatura}>{asignatura}</option>
              {/each}
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="filterEstudiante" class="form-label">Filtrar por ID de Estudiante:</label>
            <select
              id="filterEstudiante"
              class="form-select"
              bind:value={filterEstudiante}
            >
              <option value="">Todos</option>
              {#each uniqueEstudiantes as estudiante}
                <option value={estudiante}>{estudiante}</option>
              {/each}
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="filterNombreEstudiante" class="form-label">Filtrar por Nombre de Estudiante:</label>
            <select
              id="filterNombreEstudiante"
              class="form-select"
              bind:value={filterNombreEstudiante}
            >
              <option value="">Todos</option>
              {#each uniqueNombresEstudiante as nombreEstudiante}
                <option value={nombreEstudiante}>{nombreEstudiante}</option>
              {/each}
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="column-selection mb-3">
      <label class="form-label" for="">Columnas a visualizar:</label>
      <div class="form-check mb-2">
        <input
          class="form-check-input"
          type="checkbox"
          id="toggleAllColumns"
          checked={allVisibleColumnsSelected}
          on:change={toggleAllColumns}
        />
        <label class="form-check-label" for="toggleAllColumns">
          Seleccionar/Desmarcar Todo (Visibles)
        </label>
      </div>
      <div class="d-flex flex-wrap">
        {#each allColumns as column} <!-- Iterate over allColumns -->
          <div class="form-check me-3">
            <input
              class="form-check-input"
              type="checkbox"
              id="checkbox-{column}"
              checked={selectedColumns.includes(column) && !columnsWithAllNulls.has(column)}
              on:change={() => toggleColumn(column)}
            />
            <label class="form-check-label" for="checkbox-{column}">
              {column}
            </label>
          </div>
        {/each}
      </div>
    </div>

    {#if filteredNotes.length > 0}
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1; width: 50px;">Acciones</th> <!-- New header for the button column -->
              {#each selectedColumns as header}
                <th style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">{header}</th>
              {/each}
            </tr>
          </thead>
          <tbody>
            {#each filteredNotes as note}
              <tr>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" on:click={() => showConsolidatedInfo(note)}>
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </td>
                {#each selectedColumns as column}
                  <td>{note[column]}</td>
                {/each}
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {:else}
      <p>No hay notas para mostrar con los filtros aplicados.</p>
    {/if}
    <button class="btn btn-primary mt-3" on:click={closeViewer}>Cerrar</button>
  </div>
    {#if showConsolidatedViewer}
      <ConsolidatedInfoViewer note={selectedNoteForConsolidation} on:close={() => showConsolidatedViewer = false} />
    {/if}
</div>

<style>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    max-width: 95%; /* Increased max-width */
    max-height: 95%; /* Increased max-height */
    width: auto; /* Allow content to dictate width up to max-width */
    height: auto; /* Allow content to dictate height up to max-height */
    overflow: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    position: relative;
  }

  .table-responsive {
    margin-top: 20px;
  }

  h2 {
    color: #333;
    margin-bottom: 15px;
  }

  .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
  }

  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  .filters .form-label,
  .column-selection .form-label {
    font-weight: bold;
  }
</style>
