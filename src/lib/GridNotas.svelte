<script lang="ts">
	import { _URL } from './../Stores.js';
  import axios from "axios";
  import type { objetoNotas,asignaturas,notas } from "./Formulario.svelte";

  export let _asignaturas:asignaturas = [];
  export let _notas:notas = [];
  export let estudiante:string;
  export let periodo:string;
  export let grado:string;

  let NotasVal;

  const setNotas = (node): void => {
    if (node && _notas.length > 0) {
      node.querySelectorAll("input").forEach((el: HTMLInputElement) => {
        let asignatura = el.dataset.asignatura;
        console.log({ asignatura });
        let index = _notas.findIndex(
          (n: objetoNotas) => n.asignatura === asignatura
        );
        console.log({ index });
        if (index !== -1)
          el.value =
            _notas[index].valoracion && _notas[index].valoracion.toString();
      });
    }
  };

  $: if (_notas.length > 0) setNotas(NotasVal);


  interface Valoracion{
    estudiante:string;
    periodo:string;
    grado:string;
    valoracion:number;
    docente:string;
    asignatura:string;
    
  }

  const guardarValoracion=async(datos:Valoracion):Promise<Boolean>=>{
    const {data} = await axios.post(`${$_URL}guardarValoracionIndividual.php`,datos)
    return data.msg==="ok";
  }


  const setValoracion=async (e):Promise<void>=>{
    let button=e.target.closest("button");
    let data:Valoracion=button.dataset;
    let valoracion=button.parentElement.previousElementSibling.children[0].value;
    data={...data,valoracion};
    console.log(data)
    await guardarValoracion(data);
  }
</script>

<main class="mt-5">
  {#if _asignaturas.length > 0 && _notas.length > 0}
    <article class="a-grid" bind:this={NotasVal}>
      {#each _asignaturas as { docente, asignatura, nombres }, index}
        <section
          class="position-relative m-2"
          class:colorCol={index % 2 === 0}
          class:colorCol2={index % 2 !== 0}
        >
          {asignatura}
          <span
            class="position-absolute top-0 start-100 translate-middle badge text-bg-info rounded-pill"
          >
            <a
              href="#!"
              class="text-decoration-none"
              title="{asignatura} {docente} -> {nombres}"
              ><i class="fa-solid fa-info" /></a
            >
            <span class="visually-hidden">unread messages</span>
          </span>
        </section>
        <aside class="m-2">
          <input
            type="text"
            class="form-control"
            data-asignatura={asignatura}
            title={asignatura}
          />
        </aside>
        <span class="mx-auto">
          <button class="btn btn-warning mt-2" title={`Guardar ${asignatura}`}
          data-asignatura={asignatura}
          data-estudiante={estudiante}
          data-periodo={periodo}
          data-grado={grado}
          data-docente={docente}
          on:click={setValoracion}
            ><i class="fa-solid fa-cloud-arrow-up" 
            
            /></button
          >
        </span>
      {/each}
    </article>
  {/if}
</main>

<style>
  .a-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 0.3fr;
    grid-template-rows: auto;
    row-gap: 2px;
    column-gap: 15px;
  }
  .a-grid > section {
    border-bottom: 1px solid black;
  }
  .colorCol {
    color: indigo;
    font-weight: bold;
  }

  .colorCol2 {
    color: darkcyan;
    font-weight: bold;
  }
</style>
