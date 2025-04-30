<h2 class="pagina__heading"><?php echo $titulo; ?></h2>

<div class="dashboard__contenedor">
    <?php if (!empty($eventos)) { ?>
        <table class="table">
            <thead class="table__thead-eventos">
                <tr>
                    <th scope="col" class="table__th">Nombre</th>
                    <th scope="col" class="table__th">Día</th>
                    <th scope="col" class="table__th">Hora</th>
                    <th scope="col" class="table__th">Ponente</th>
                </tr>
            </thead>
            <tbody class="table__tbody">
                <?php foreach ($eventos as $evento) { ?>
                    <tr class="table__tr">
                        <td class="table__td">
                            <?php echo $evento->nombre; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->dia->nombre; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->hora->hora; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->ponente->nombre . ' ' . $evento->ponente->apellido; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center">No Hay Registros Aún</p>
    <?php } ?>
</div>