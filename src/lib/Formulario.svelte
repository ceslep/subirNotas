<script lang="ts">
  import { onMount } from "svelte";
  import { _URL } from "./../Stores.js";
  import axios from "axios";

  let sedes: asignaciones = [];
  let _grados: grados = [];
  let _estudiantes: estudiantes = [];
  let _periodos: periodos = [];
  let _asignaturas: asignaturas = [];
  let _notas: notas = [];

  onMount(async (): Promise<void> => {
    sedes = await getAsignaciones();
    _periodos = await getPeriodos();
  });

  type asignaciones = objetoAsignaciones[];
  type grados = objetoGrados[];
  type estudiantes = objetoEstudiantes[];
  type periodos = objetoPeriodos[];
  type asignaturas = objetoAsignaturas[];
  type notas = objetoNotas[];

  interface objetoAsignaciones {
    ind: string;
    sede: string;
  }
  interface objetoGrados {
    nivel: string;
    numero: string;
    grado: string;
  }

  interface objetoEstudiantes {
    estudiante: string;
    nombres: string;
    nivel: number;
    numero: number;
  }

  interface objetoPeriodos {
    nombre: string;
  }

  const getPeriodos = async (): Promise<periodos> => {
    const { data } = await axios.get(`${$_URL}getPeriodos.php`);
    const _periodos = data
      .filter((d: any) => d.periodo !== "CINCO" && d.periodo !== "MINIMAS")
      .map((item: any) => {
        return {
          nombre: item.periodo,
        };
      });
    return _periodos;
  };
  const getAsignaciones = async (): Promise<asignaciones> => {
    const { data }: any = await axios.post(`${$_URL}getasignacion.php`);
    const _asignaciones: asignaciones = data.map((item: objetoAsignaciones) => {
      return {
        ind: item.ind,
        sede: item.sede,
      };
    });
    return _asignaciones;
  };

  const getGrados = async (): Promise<grados> => {
    const { data }: any = await axios.post(`${$_URL}generaGrupos.php`, {
      sede: formData.sede,
    });
    const _grados: grados = data.map((item: objetoGrados) => {
      return {
        nivel: item.nivel,
        numero: item.numero,
        grado: item.grado,
      };
    });
    return _grados;
  };

  interface FormData {
    sede: string;
    estudiante: string;
    docente: string;
    asignatura: string;
    grado: string;
    periodo: string;
    valoracion: string;
    year: string;
  }

  interface objetoAsignaturas {
    docente: string;
    asignatura: string;
    nombres: string;
  }

  interface objetoNotas {
    asignatura: string;
    valoracion: number;
  }

  const formData: FormData = {
    sede: "",
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

  const changeSede = async (): Promise<void> => {
    _grados = await getGrados();
  };

  const getEstudiantes = async (): Promise<estudiantes> => {
    const { nivel, numero } =
      _grados[
        _grados.findIndex((g: objetoGrados) => g.grado === formData.grado)
      ];
    const { data }: any = await axios.post(`${$_URL}getEstudiantes.php`, {
      asignacion: formData.sede,
      sede: "",
      nivel,
      numero,
      year: new Date().getFullYear(),
      periodo: "",
    });
    const _estudiantes: estudiantes = data.map((item: objetoEstudiantes) => {
      return {
        estudiante: item.estudiante,
        nombres: item.nombres,
      };
    });
    return _estudiantes;
  };

  const changeGrado = async (): Promise<void> => {
    _estudiantes = [];
    _estudiantes = await getEstudiantes();
  };

  const getAsignaturas = async (): Promise<asignaturas> => {
    const { nivel, numero } =
      _grados[
        _grados.findIndex((g: objetoGrados) => g.grado === formData.grado)
      ];
    const { data } = await axios.post(`${$_URL}getNotasSubir.php`, {
      sede: formData.sede,
      nivel,
      numero,
    });
    console.log({ data });
    const _asignaturas: asignaturas = data.map((item: objetoAsignaturas) => {
      return {
        docente: item.docente,
        asignatura: item.asignatura,
        nombres: item.nombres,
      };
    });
    return _asignaturas;
  };

 

  const getNotas = async (): Promise<notas> => {
    const {data} = await axios.post(`${$_URL}getNotasIndividual.php`, 
       JSON.stringify({
        estudiante: formData.estudiante,
        asignaturas: `${_asignaturas
          .map((a: objetoAsignaturas) => `'${a.asignatura}'`)
          .join(",")}`,
        periodo: formData.periodo,
        }));
    
    console.log({ data });
    const _notas = data.map((item: objetoNotas) => {
      return {
        asignatura: item.asignatura,
        valoracion: item.valoracion,
      };
    });
    return _notas;
  };

  const changePeriodo = async (): Promise<void> => {
    _asignaturas=[];
    _notas=[];
    _asignaturas = await getAsignaturas();
    _notas = await getNotas();
    console.log({ _notas });
  };

  const setNotas=(node):void=>{
   node.querySelectorAll("input").forEach((el:HTMLInputElement)=>{
      let asignatura=el.dataset.asignatura;
      let index=_notas.findIndex((n:objetoNotas)=>n.asignatura===asignatura)
      console.log({index});
      if (index!==-1)
      el.value=(_notas[index].valoracion).toString();
   });
  }

  let NotasVal;

  $:if(_notas.length>0) setNotas(NotasVal);
</script>

<main class="container">
  <form class="row g-3" on:submit|preventDefault={handleSubmit}>
    <input type="hidden" name="currentYear" value={formData.year} />
    <!-- Campo oculto con el año actual -->
    <div class="col-md-6">
      <label for="sede" class="form-label">Sede:</label>
      <select
        id="sede"
        class="form-select"
        bind:value={formData.sede}
        on:change={changeSede}
      >
        {#each sedes as sede}
          <option value={sede.ind}>{sede.sede}</option>
        {/each}
      </select>
    </div>
    <div class="col-md-6">
      <label for="grado" class="form-label">Grado:</label>
      <select
        id="grado"
        class="form-select"
        bind:value={formData.grado}
        on:change={changeGrado}
      >
        <option value="" />
        {#each _grados as grado}
          <option value={grado.grado}>{grado.grado}</option>
        {/each}
      </select>
    </div>
    <div class="col-md-6">
      <label for="estudiante" class="form-label">Estudiante:</label>
      <select
        id="estudiante"
        class="form-select"
        bind:value={formData.estudiante}
        on:change={changeEstudiante}
      >
        <option value="" />
        {#each _estudiantes as estudiante}
          <option
            data-estudiante={JSON.stringify(estudiante)}
            value={estudiante.estudiante}>{estudiante.nombres}</option
          >
        {/each}
      </select>
    </div>

    <div class="col-md-6">
      <label for="periodo" class="form-label">Periodo:</label>
      <select
        id="periodo"
        class="form-select"
        bind:value={formData.periodo}
        on:change={changePeriodo}
      >
        <option value="" />
        {#each _periodos as periodo}
          <option value={periodo.nombre}>{periodo.nombre}</option>
        {/each}
      </select>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Enviar</button>
    </div>
  </form>
  {#if _asignaturas.length>0}
  <article class="a-grid" bind:this={NotasVal}>
    {#each _asignaturas as { docente, asignatura, nombres }, index}
      <section class="position-relative m-2" class:colorCol={index % 2 === 0}>
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
        <input type="text" class="form-control" data-asignatura="{asignatura}" title="{asignatura}"/>
      </aside>
    {/each}
  </article>
  {/if}
</main>

<style>
  .a-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
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
</style>
