<script>
  export let show;
</script>

{#if show}
<div class="spinner-border spinner-border-sm" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
{/if}  