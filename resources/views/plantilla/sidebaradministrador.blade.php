<div class="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav">

                    <li @click="menu=42" class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa fa-dashboard"></i> ESCRITORIO</a>
                    </li>
                    <li class="nav-title">
                        Operaciones
                    </li>

                    <!--Menu Restaurante-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-briefcase"  ></i> RESTAURANTE</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=13" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Info. Local</a>
                            </li>
                            <li @click="menu=14" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Sucursales</a>
                            </li>
                            <li @click="menu=41" class="nav-item">
                            <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Puntos de Venta</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-dollar"></i> FINANZAS</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=16" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Apertura/Cierre Caja</a>
                            </li>
                        </ul>
                    </li>

                    <!--Menu Ventas-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-shopping-cart"></i> VENTAS</a>
                        <ul class="nav-dropdown-items">

                            <li @click="menu=39" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Ventas al Contado</a>
                            </li>
                            <!--<li @click="menu=53" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Ventas en Mesa</a>
                            </li>-->
                            <li @click="menu=6" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Clientes</a>
                            </li>
                            <li @click="menu=54" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Reporte de Ventas</a>
                            </li>
                        </ul>
                    </li>

                    <!--<li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-shopping-cart"></i> EVENTOS</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=16" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Apertura/Cierre Caja</a>
                            </li>
                            <li @click="menu=53" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Vender</a>
                            </li>
                            <li @click="menu=6" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Clientes</a>
                            </li>
                            <li @click="menu=54" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Reporte de Ventas</a>
                            </li>
                        </ul>
                    </li>-->

                    <!--Menu Compras-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-shopping-bag"></i> COMPRAR</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=3" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Registrar Compra</a>
                            </li>
                            
                        </ul>
                    </li>

                    <!--Menu Inventario-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-file-text"></i> INVENTARIO</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=24" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Almacenes</a>
                            </li>
                            <li @click="menu=25" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Stock</a>
                            </li>
                        </ul>
                    </li>

                    <!--Menu-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-tags"></i> MENU</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=46" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Registrar menu</a>
                            </li>
                            <li @click="menu=47" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Categorias de Menu</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-cutlery"></i> BEBIDAS</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=2" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Registrar Bebidas</a>
                            </li>
                            <li @click="menu=1" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Categorias de Bebidas</a>
                            </li>
                            <li @click="menu=4" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Mis Proveedores</a>
                            </li>
                            <!--<li @click="menu=27" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Medidas</a>
                            </li>-->
                        </ul>
                    </li>

                    <!--Menu Reportes-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-line-chart"></i> REPORTES</a>
                        <ul class="nav-dropdown-items">

                            <li @click="menu=50" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Ventas Detalladas</a>
                            </li>

                            <li @click="menu=51" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Mi Inventario</a>
                            </li>
                        </ul>
                    </li>


                    <!--Menu Usuarios-->

                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-lock"></i> ACCESOS</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=7" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Usuarios</a>
                            </li>
                        </ul>
                    </li>

                    <!--Menu SIAT-->
                    <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-info"></i>SIAT</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=31" class="nav-item">
                            <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i>Sinc. Actividades</a>
                        </li>
                        <li @click="menu=34" class="nav-item">
                            <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i>Sinc. Servicios</a>
                        </li>
                        <li @click="menu=37" class="nav-item">
                            <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i>Sinc. Unidad Medida</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-shopping-cart"></i> VENTAS OFFLINE</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=38" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Eventos Significativos</a>
                            </li>
                            <li @click="menu=5" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-list" style="font-size: 11px;"></i> Ventas Offline</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>