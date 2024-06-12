<div class="admin-container">

    <h1 class="title">Mes commandes</h1>

    <?php
    include(PATH_ROOT . '/views/_templates/_message.html.php');
    use App\Model\Order;
use Core\Session\Session;

    $user_id = Session::get(Session::USER)->id;

    foreach ($orders as $status => $order) :
        switch ($status) {
            case Order::IN_CART:
                $status = "Panier en cours";
                break;
            case Order::VALIDATED:
                $status = "Commande validée";
                break;
            case Order::PENDING:
                $status = "En attente ";
                break;
            case Order::PREPARING:
                $status = "En preparation";
                break;
            case Order::DELIVERED:
                $status = "Livrée";
                break;
            case Order::CANCELED:
                $status = "Annulée";
                break;
        }

    ?>
        <H2 class="sub-title mt-5"><?php echo $status ?></H2>
        <table>
            <thead>
                <tr>
                    <th class="footer-description">Numéro de commande</th>
                    <th class="footer-description">Date de commande</th>
                    <th class="footer-description">Date de livraison</th>
                    <th class="footer-description">Déscriptif</th>
                    <th class="footer-description">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order as $row) :
                    $date_order = new DateTime($row->date_order);
                    $date_order_format = $date_order->format('d/m/Y H:i');
                    $date_delivered = new DateTime($row->date_delivered);
                    $date_delivered_format = $date_delivered->format('d/m/Y H:i');
                ?>
                    <tr>
                        <td class="footer-description"><?= $row->order_number ?></td>
                        <td class="footer-description"><?= $date_order_format ?></td>
                        <td class="footer-description"><?= $date_delivered_format ?></td>
                        <td class="footer-description">
                            <?php foreach ($row->order_rows as $order_row) : ?>
                                <p><?= $order_row->quantity ?> x <?= $order_row->pizza->name ?></p>
                            <?php endforeach ?>
                        </td>
                        <?php if ($row->status === Order::IN_CART) : ?>
                            <td class="footer-description">
                                <a href="/order/<?= $user_id ?>" class=" btn btn-warning">Voir le panier</a>
                            </td>
                        <?php elseif ($row->status === Order::VALIDATED) : ?>
                            <td class="footer-description">
                                <a href="/user/order/cancel/<?= $row->id ?>" class=" btn btn-danger">Annuler</a>
                            </td>
                        <?php elseif ($row->status === Order::CANCELED) : ?>
                            <td class="footer-description">
                                <a href="/user/order/reactived/<?= $row->id ?>" class=" btn btn-success">Réactiver</a>
                            </td>
                        <?php else : ?>
                            <td class="footer-description"> </td>
                        <?php endif; ?>
                    </tr>

                <?php endforeach ?>
            </tbody>
        </table>

    <?php endforeach; ?>
</div>