<script context="module" lang="ts">
  export interface objetoNotas {
    asignatura: string;
    valoracion: number;
  }

  export interface objetoAsignaturas {
    docente: string;
    asignatura: string;
    nombres: string;
  }

  export type asignaturas = objetoAsignaturas[];
  export type notas = objetoNotas[];

  export function delay(ms:number) {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }
</script>

<script lang="ts">
  import { onMount } from "svelte";
  import { _URL } from "./../Stores.js";
  import axios from "axios";
  import GridNotas, { setValoracion } from "./GridNotas.svelte";
  import Swal from "sweetalert2";
  import MiniSpinner from "./MiniSpinner.svelte";
  import BotonFlotante from "./BotonFlotante.svelte";

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
    try {
      const { data }: any = await axios.post(`${$_URL}getasignacion.php`);
      const _asignaciones: asignaciones = data.map(
        (item: objetoAsignaciones) => {
          return {
            ind: item.ind,
            sede: item.sede,
          };
        }
      );
      return _asignaciones;
    } catch (error) {
      let { message } = error;
      Swal.fire({
        icon: "error",
      });
    }
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
    const { data } = await axios.post(
      `${$_URL}getNotasIndividual.php`,
      JSON.stringify({
        estudiante: formData.estudiante,
        asignaturas: `${_asignaturas
          .map((a: objetoAsignaturas) => `'${a.asignatura}'`)
          .join(",")}`,
        periodo: formData.periodo,
      })
    );

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
    _asignaturas = [];
    _notas = [];
    _asignaturas = await getAsignaturas();
    _notas = await getNotas();
    console.log({ _notas });
  };

  const changeEstudiante = async (): Promise<void> => {};

  
  const guardarTodo = async (): Promise<void> => {
    const buttons: any = document.querySelectorAll(".guardarTodo");
    console.log({ buttons });
    buttons.forEach((button: HTMLButtonElement) => {
     setValoracion(button)
    });
    Swal.fire({
      title: "Guardando...",
      text: "Guardando la Valoración",
    });
  };
</script>

<main class="container">
  <nav class="sticky-top bg-body-tertiary bg-light">
    <form class="row g-3" on:submit|preventDefault={handleSubmit}>
      <input type="hidden" name="currentYear" value={formData.year} />
      <!-- Campo oculto con el año actual -->
      <div class="col-md-6">
        <label for="sede" class="form-label"
          >Sede:<MiniSpinner show={sedes.length === 0} /></label
        >
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
        <label for="grado" class="form-label"
          >Grado:<MiniSpinner
            show={_grados.length === 0 && formData.sede !== ""}
          /></label
        >
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
        <label for="estudiante" class="form-label"
          >Estudiante:<MiniSpinner
            show={_estudiantes.length === 0 && formData.grado !== ""}
          /></label
        >
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
    </form>
  </nav>
  <div class="mt-3">
    <MiniSpinner
      show={_asignaturas.length === 0 &&
        _notas.length === 0 &&
        formData.periodo !== ""}
    />
  </div>
  <GridNotas
    {_asignaturas}
    {_notas}
    estudiante={formData.estudiante}
    periodo={formData.periodo}
    grado={formData.grado}
  />
</main>

<BotonFlotante
  show={_asignaturas.length > 0 && _notas.length > 0}
  on:guardar={guardarTodo}
/>
