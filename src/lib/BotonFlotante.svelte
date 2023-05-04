<script>
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();
  export let show;
</script>

{#if show}
  <div
    class="floating-button"
    on:click={() => {
      dispatch("guardar");
    }}
    on:keydown={()=>{}}
  >
    <a href="#!"><i class="fa-solid fa-floppy-disk" /></a>
  </div>
{/if}

<style>
  .floating-button {
    position: fixed;
    bottom: 15px;
    right: 15px;
    background-color: #ff5733;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    text-align: center;
    box-shadow: 2px 2px 3px #999;
  }

  .floating-button a {
    display: inline-block;
    vertical-align: middle;
    line-height: 60px;
    color: #fff;
    font-size: 20px;
    text-decoration: none;
  }
</style>
