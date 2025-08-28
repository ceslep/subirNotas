<script>
  export let show;
  export let color = 'black'; // New property
</script>

{#if show}
<div class="spinner-border spinner-border-sm" role="status" style="color: {color};">
    <span class="visually-hidden">Loading...</span>
  </div>
{/if}