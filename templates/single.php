<main class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img width="100%" title="<?php echo $picture["title"] ?>" src="<?php  echo $picture["url"] ?>" />
        </div>
        <div class="col-md-6">
            <form method="post" action="/image/<?php  echo $picture["id"] ?>/edit">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input id="title" name="title" value="<?php  echo $picture["title"] ?>" class="form-control" placeholder="Enter the title here."/>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
            <form class="mt-5" method="post" action="/image/<?php  echo $picture["id"] ?>/delete">
            <div class="form-group">
                    <label for="title">Danger zone</label>
                </div>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</main>