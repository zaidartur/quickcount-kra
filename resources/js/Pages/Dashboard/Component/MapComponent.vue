<template>
    <ol-map
        :loadTilesWhileAnimating="true"
        :loadTilesWhileInteracting="true"
        style="height: 500px"
    >
        <ol-view
            ref="view"
            :center="center"
            :zoom="zoom"
            :projection="projection"
        />
  
        <ol-tile-layer>
            <ol-source-osm />
        </ol-tile-layer>
    
        <ol-interaction-select
            @select="featureSelected"
            :condition="selectCondition"
            :filter="selectInteactionFilter"
        >
            <ol-style>
                <ol-style-stroke color="green" :width="5"></ol-style-stroke>
                <ol-style-fill color="rgba(255,255,255,0.5)"></ol-style-fill>
            </ol-style>
        </ol-interaction-select>
  
        <ol-vector-layer>
            <ol-source-vector
                ref="desakec"
                url="http://127.0.0.1:8000/uploads/desakec.geojson"
                :format="geoJson"
                :projection="projection"
            >
            </ol-source-vector>
  
            <ol-style>
                <ol-style-stroke color="black" :width="1"></ol-style-stroke>
                <ol-style-fill color="rgba(255,255,255,0.1)"></ol-style-fill>
            </ol-style>
        </ol-vector-layer>

        <ol-overlay :position="selectedCityPosition"
            v-if="selectedCityName != '' && !drawEnable"
        >
            <template v-slot="slotProps">
                <div class="overlay-content">
                    {{ selectedCityName }} {{ slotProps }}
                </div>
            </template>
        </ol-overlay>
    </ol-map>
</template>

<script setup>
import markerIcon from "@/Assets/marker.png";
import { ref, inject } from "vue";

const center = ref([111, -7.62]);
const projection = ref("EPSG:4326");
const zoom = ref(11);
const view = ref(null);
const desakec = ref(null)

const selectedCityName = ref("");
const selectedCityPosition = ref([]);

const format = inject("ol-format");

const geoJson = new format.GeoJSON();

const selectConditions = inject("ol-selectconditions");

const selectCondition = selectConditions.pointerMove;

const featureSelected = (event) => {
    console.log(event);
    if (event.selected.length == 1) {
        selectedCityPosition.value = extent.getCenter(
            event.selected[0].getGeometry().extent_,
        );
        selectedCityName.value = event.selected[0].values_.nama;
    } else {
        selectedCityName.value = "";
    }
};

const selectInteactionFilter = (feature) => {
    return feature.values_.name != undefined;
};
</script>