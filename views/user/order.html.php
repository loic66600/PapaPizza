<?php use Core\Session\Session; ?>
<main class="d-flex flex-column align-items-center">
  <h1 class="title title-detail">Mon panier vide</h1>
  <!-- si j'ai un message d'erreur on l'affiche -->
  <?php include (PATH_ROOT .'/views/_templates/_message.html.php'); ?>

  <?php if ($count_row <= 0) : ?>
    <div class="alert alert-info">
      Votre panier est vide
    </div>
  <?php else :
    $dateTime = new DateTime($order->date_order);
  ?>
    <div>
      <p class="header-description">Commande : <?= $order->order_number ?></p>
      <p class="header-description">Commande passé le : <?= $dateTime->format("d/m/Y H:i:s") ?></p>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th class="footer-description">Nom de pizza</th>
          <th class="footer-description">Nombre de pizza</th>
          <th class="footer-description">Modifier quantité</th>
          <th class="footer-description">Prix total</th>
          <th class="footer-description">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order->order_rows as $row) :
          //on formate l'affichage du nombre de pizza
          $total_pizza = $count_row > 1 ? $count_row . ' pizzas' : $count_row . ' pizza';
        ?>
          <tr class="footer-description">
            <td class="footer-description"><?= $row->pizza->name ?></td>
            <td class="footer-description"><?= $row->quantity ?></td>
            <td class="footer-description">
              <!-- afficher la quantité avec possibilité de modifier avec un formulaire -->
              <form action="/order/update/<?= $row->id ?>" method="POST">
                <input type="hidden" name="order_row_id" value="<?= $row->id ?>">
                <input type="hidden" name="pizza_id" value="<?= $row->pizza_id ?>">
                <input type="hidden" name="size_id" value="<?= $row->size_id ?>">
                <input type="number" name="quantity" value="<?= $row->quantity ?>" min="1" max="10" class="quantity">
                <button class="call-action py-1 px-2" type="submit">
                  <i class="bi bi-check-circle"></i>
                </button>
              </form>
            </td>
            <td class="footer-description"><?= number_format($row->price, 2, ',', '.') ?> €</td>
            <td class="footer-description">
              <form action="/order-row/delete/<?= $row->id ?>" method="POST">
                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                <input type="hidden" name="pizza_id" value="<?= $row->pizza_id ?>">
                <button class="call-action py-1 px-2" type="submit">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>

        <?php endforeach ?>

        <tr class="footer-description">
         <td class="footer-description"></td> 
         <td class="footer-description"></td> 
         <td class="footer-description">Nombre : <?= $total_pizza ?></td> 
         <td class="footer-description">Total : <?= number_format($total, 2, ',', '.') ?> €</td> 
         <td class="footer-description">
          <a class="btn btn-warning" href="/order/confirm/<?= $order->id ?>">Payer <?= number_format($total, 2, ',', '.') ?> €</a>
         </td> 
      </tbody>
    </table>
  <?php endif; ?>
</main>