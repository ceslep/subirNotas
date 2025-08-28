<script>
  import { createEventDispatcher } from 'svelte';

  export let note = {};

  const dispatch = createEventDispatcher();

  function closeViewer() {
    dispatch('close');
  }
</script>

<div class="modal-overlay" on:click={closeViewer} on:keydown={(e) => e.key === 'Escape' && closeViewer()} tabindex="-1">
  <div class="modal-content" on:click|stopPropagation on:keydown={(e) => e.key === 'Escape' && closeViewer()}>
    <h2>Información Consolidada de la Fila</h2>
    <div class="consolidated-info">
      {#each Object.entries(note) as [key, value]}
        <p><strong>{key}:</strong> {value}</p>
      {/each}
    </div>
    <button class="btn btn-primary mt-3" on:click={closeViewer}>Cerrar</button>
  </div>
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
    z-index: 10000; /* Higher z-index than NotasViewer modal */
  }

  .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    max-height: 80%;
    overflow: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    position: relative;
  }

  .consolidated-info p {
    margin-bottom: 5px;
  }
</style>