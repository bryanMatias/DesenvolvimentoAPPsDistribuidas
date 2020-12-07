<template>
  <ul
    class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
    id="accordionSidebar"
  >
    <!-- Sidebar - Brand -->
    <a
      class="sidebar-brand d-flex align-items-center justify-content-center"
      href="/#/welcome"
    >
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-hamburger"></i>
      </div>
      <div class="sidebar-brand-text mx-3">Food @ Home</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0" />

    <!-- Nav Item - Dashboard -->
    <li class="nav-item" v-if="isNotClient">
      <a class="nav-link" href="/#/dashboard">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a
      >
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider" />

    <!-- Heading -->
    <div class="sidebar-heading">Settings</div>

    <!-- Nav Item - Profile -->
    <li class="nav-item">
      <a class="nav-link" href="/#/welcome">
        <i class="fas fa-fw fa-user"></i>
        <span>Profile</span>
      </a>
    </li>

    <!-- Nav Item - About -->
    <li class="nav-item">
      <a class="nav-link" href="/#/welcome">
        <i class="fas fa-fw fa-hands-helping"></i>
        <span>About</span>
      </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Heading -->
    <div class="sidebar-heading">Web Developer</div>

    <!-- Nav Item - About -->
    <li class="nav-item">
      <a class="nav-link" href="/#/dev/users">
        <i class="fas fa-table"></i>
        <span>Users table</span>
      </a>
    </li>

    <!-- Nav Item - About -->
    <li class="nav-item">
      <a class="nav-link" href="/#/welcome">
        <i class="fas fa-table"></i>
        <span>Products table</span>
      </a>
    </li>

    <!-- Nav Item - About -->
    <li class="nav-item">
      <a class="nav-link" href="/#/welcome">
        <i class="fas fa-table"></i>
        <span>Orders table</span>
      </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />

    <div v-if="isManager">
      <!-- Heading -->
      <div class="sidebar-heading">Manager</div>

      <!-- Nav Item - Profile -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-user"></i>
          <span>Profile</span>
        </a>
      </li>

      <!-- Nav Item - About -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-hands-helping"></i>
          <span>About</span>
        </a>
      </li>
    </div>

    <div v-else-if="isCooker">
      <!-- Heading -->
      <div class="sidebar-heading">Cooker</div>

      <!-- Nav Item - Profile -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-user"></i>
          <span>Profile</span>
        </a>
      </li>

      <!-- Nav Item - About -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-hands-helping"></i>
          <span>About</span>
        </a>
      </li>
    </div>

    <div v-else-if="isDeliveryMan">
      <!-- Heading -->
      <div class="sidebar-heading">DeliveryMan</div>

      <!-- Nav Item - Profile -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-user"></i>
          <span>Profile</span>
        </a>
      </li>

      <!-- Nav Item - About -->
      <li class="nav-item">
        <a class="nav-link" href="/#/welcome">
          <i class="fas fa-fw fa-hands-helping"></i>
          <span>About</span>
        </a>
      </li>
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
  </ul>
</template>

<script>
export default {
  computed: {
    isNotClient() {
      return this.$store.state.user.type != "C";
    },
    isManager() {
      return this.$store.state.user.type == "EM";
    },
    isCooker() {
      return this.$store.state.user.type == "EC";
    },
    isDeliveryMan() {
      return this.$store.state.user.type == "ED";
    },
  },
};
</script>

<style>
</style>