<template>
    <div id="app">
        <router-view />
    </div>
</template>

<script setup>
// App.vue now uses router-view to render the appropriate layout
</script>

<style>
/* Global styles */
body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto",
        "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans",
        "Helvetica Neue", sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.router-link-active {
    color: #1f2937 !important;
    font-weight: 600;
}
</style>
