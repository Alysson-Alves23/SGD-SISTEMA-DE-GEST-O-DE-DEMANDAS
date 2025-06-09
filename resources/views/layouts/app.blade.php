<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Demandas')</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .page-container {
            width: 100%;
            max-width: 1200px;
            justify-content: center;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2em;
            color: #333;
        }

        .user-info {
            font-size: .9em;
            color: #555;
        }

        .user-info a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }

        .user-info a:hover {
            text-decoration: underline;
        }

        .filter-nav {
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .filter-button,
        .status-filter-select {
            padding: 10px 18px;
            background-color: #e9ecef;
            border: 1px solid #007bff;
            border-radius: 4px;
            text-decoration: none;
            font-size: .95em;
            font-weight: 500;
            transition: background-color .2s ease, color .2s ease;
            color: #007bff;
            cursor: pointer;
        }

        .filter-button {
            margin: 0;
        }

        .filter-button:hover,
        .status-filter-select:hover {
            background-color: #007bff;
            color: #fff;
        }

        .filter-button.selected {
            background-color: #007bff;
            color: #fff;
            font-weight: 700;
        }

        .primary-action-button {
            padding: 10px 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: .95em;
            font-weight: 500;
            transition: background-color .2s ease;
            margin-left: auto;
        }

        .primary-action-button:hover {
            background-color: #0056b3;
        }

        .status-filter-select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007bff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px auto;
            padding-right: 30px;
        }

        .demands-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .demand-item {
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .08);
            cursor: pointer;
            transition: box-shadow .2s ease, transform .2s ease;
            border: 1px solid #e0e0e0;
        }

        .demand-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
            transform: translateY(-3px);
        }

        .demand-item .demand-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .demand-item .demand-id {
            font-weight: 700;
            color: #007bff;
            font-size: 1.1em;
        }

        .demand-item .demand-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.4em;
        }

        .demand-item .demand-status {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 15px;
            font-size: .8em;
            font-weight: 700;
            border-width: 1.5px;
            border-style: solid;
            background-color: transparent;
        }

        .status-aberta {
            border-color: #ffc107;
            color: #c69500;
        }

        .status-em-andamento {
            border-color: #17a2b8;
            color: #0f6674;
        }

        .status-concluida {
            border-color: #28a745;
            color: #155724;
        }

        .status-pendente {
            border-color: #6c757d;
            color: #383d41;
        }

        .status-em-pausa {
            border-color: #fd7e14;
            color: #bf5f0d;
        }

        .status-fechado {
            border-color: #454d55;
            color: #1b1e21;
        }

        .status-desconhecido {
            border-color: #ccc;
            color: #888;
        }

        .demand-item .demand-details p {
            font-size: .9em;
            color: #555;
            margin: 5px 0;
        }

        .no-demands {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .08);
        }

        .no-demands p {
            font-size: 1.2em;
            color: #777;
        }

        .loading-message {
            text-align: center;
            font-size: 1.2em;
            padding: 40px;
        }

        .submit-button {
            padding: 12px 25px;
            background-color: #007bff;
          
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .submit-button:hover {
            background-color: #0056b3;
            
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="page-container">
        <header class="page-header">
            <h1 id="pageTitle">@yield('page-title', 'Sistema de Demandas')</h1>
            <div class="user-info" id="userInfoDisplay"></div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>