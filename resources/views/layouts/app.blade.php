<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>@yield('title', 'Aplikasi Akuntansi')</title>
    <style>
        /* Sidebar styling */
        #sidebar {
            position: sticky;
            top: 0;
            transition: margin-left 0.3s ease;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            background-color: #343a40;
        }

        #sidebar.hidden {
            margin-left: -250px;
        }

        #sidebar .nav-link {
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s;
        }

        #sidebar .nav-link:hover {
            background-color: #495057;
        }

        #sidebar .nav-link.active {
            background-color: #6c757d;
            font-weight: bold;
        }

        #sidebar .nav-link i {
            font-size: 1.2rem;
        }

        /* Navbar styling */
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-button {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px 10px;
        }

        /* Main content */
        main {
            flex-grow: 1;
            overflow-x: hidden;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            #sidebar {
                width: 200px;
            }

            #sidebar.hidden {
                margin-left: -200px;
            }

            .navbar-button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white">
            <div class="d-flex flex-column h-100 p-3">
                <h4 class="text-center">Menu</h4>
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('produk*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                            <i class="bi bi-box"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('penjualan*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                            <i class="bi bi-cart"></i> Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('pembelian*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                            <i class="bi bi-bag"></i> Pembelian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('hutang*') ? 'active' : '' }}" href="{{ route('hutang.index') }}">
                            <i class="bi bi-cash-stack"></i> Hutang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('piutang*') ? 'active' : '' }}" href="{{ route('piutang.index') }}">
                            <i class="bi bi-currency-exchange"></i> Piutang
                        </a>
                    </li>
                </ul>
                <!-- Logout button -->
                <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <!-- Navbar -->
            <nav class="navbar navbar-light bg-light px-3">
                <div class="d-flex align-items-center">
                    <!-- Tombol toggle sidebar -->
                    <button class="navbar-button" id="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="navbar-brand mb-0 h1 ms-3">Aplikasi Akuntansi</span>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript untuk toggle sidebar
        const toggleSidebarButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        toggleSidebarButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    </script>
    @yield('scripts')
</body>
</html>
