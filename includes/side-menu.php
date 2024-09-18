<!-- <div class="l-navbar show" id="nav-bar">
        <nav class="nav">
            <div> <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span
                        class="nav_logo-name">Pharma v1.0</span> </a>
                <div class="nav_list">
                    <a href="../dashboard/dashboard.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="../items/items.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Items</span>
                    </a>
                    
                    <a href="../vouchers/purchase.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Purchase</span>
                    </a>
                    <a href="../vouchers/sale.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Sale</span>
                    </a>
                    
                    <a href="../party/party.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Party</span>
                    </a>
                    <a href="../settings/price-list.php" class="nav_link">
                        <i class='bx bxs-dashboard nav_icon'></i>
                        <span class="nav_name">Price List</span>
                    </a>
                    <p class="nav_link" data-bs-toggle="collapse" href="#collapseExample" role="button">
                      <i class='bx bx-package nav_icon'></i>
                      <span class="nav_name collapse-nav">Reports <i class='bx bxs-down-arrow'></i> </span>
                  <div class="collapse" id="collapseExample">
                      <a href="../reports/report.php" class="nav_link">
                          <i class='bx bxs-user-check nav_icon'></i>
                          <span class="nav_name">Report</span>
                      </a>
                  </div>
                  </p>
                  <p class="nav_link" data-bs-toggle="collapse" href="#collapseSettings" role="button">
                      <i class='bx bx-package nav_icon'></i>
                      <span class="nav_name collapse-nav">Settings <i class='bx bxs-down-arrow'></i> </span>
                  <div class="collapse" id="collapseSettings">
                      <a href="../settings/ledger.php" class="nav_link">
                          <i class='bx bxs-user-check nav_icon'></i>
                          <span class="nav_name">Ledger</span>
                      </a>
                      <a href="../settings/voucher-master.php" class="nav_link">
                          <i class='bx bxs-user-check nav_icon'></i>
                          <span class="nav_name">Voucher</span>
                      </a>
                      <a href="../settings/ledger-assign.php" class="nav_link">
                          <i class='bx bxs-user-check nav_icon'></i>
                          <span class="nav_name">Ledger Assign</span>
                      </a>
                  </div>
                  </p>
                  <a href="../logout.php" class="nav_link">
                    <i class='bx bxs-dashboard nav_icon'></i>
                    <span class="nav_name">Sign Out</span>
                </a>
                </div>
            </div>
        </nav>
    </div> -->

    <div class="l-navbar show" id="nav-bar">
      <nav class="nav">
        <div> <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">Pharma v1.0</span> </a>
          <div class="nav_list"> 
            <a href="../dashboard/dashboard.php" class="nav_link"> 
              <i class='bx bxs-dashboard nav_icon'></i> 
              <span class="nav_name">Dashboard</span> 
            </a>

            <?php
                
                $menu   =   getPrimaryMenu($dbh);
                
                for($i=0;$i<count($menu);$i++){
            ?>
            <p class="nav_link" data-bs-toggle="collapse" href="#collapse<?php echo $i;?>" role="button"> 
                <i class='bx bx-data nav_icon'></i>
                <span class="nav_name collapse-nav"><?php echo $menu[$i][0];?> <i class='bx bxs-down-arrow'></i> </span> 
                <div class="collapse" id="collapse<?php echo $i;?>">

                <?php 
                    $subMenu=getSubMenu($menu[$i][0],$dbh);
                    //var_dump($subMenu);
                    for($j=0;$j<count($subMenu);$j++){
                ?>
                    <a href="<?php echo $subMenu[$j][2];?>" class="nav_link"> 
                      <i class='bx bxs-user-check nav_icon'></i>
                      <span class="nav_name"><?php echo $subMenu[$j][0];?></span>
                    </a>
                <?php
                    }
                ?>
                </div>
            </p> 
            <?php
                }
            ?>
            
            <!-- <a href="../user/change-password.php" class="nav_link"> <i class='bx bxs-user-check nav_icon'></i> <span
              class="nav_name">Change Password</span> 
            </a> -->

            <a href="../logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span
              class="nav_name">SignOut</span> 
            </a>

          </div>
        </div>
      </nav>
    </div>

    <?php 
    function getPrimaryMenu($dbh)
    {
        $role   =   $_SESSION["PH_USER_TYPE"];
        $q="SELECT DISTINCT(menu_title) FROM tbl_app_menu where menu_role='$role' AND menu_visibility=1";
        //echo $q;
        try {
            $result = GetData($q,$dbh);
        } catch (\Throwable $th) {
            $result = 0;
        }
        return $result;
    }
    function getSubMenu($menu,$dbh)
    {
        $role   =   $_SESSION["PH_USER_TYPE"];
        $q="SELECT menu_sub_title,menu_icon,menu_link FROM tbl_app_menu where menu_role='$role' AND menu_title='$menu' AND menu_visibility=1";

        try {
            $result = GetData($q,$dbh);
        } catch (\Throwable $th) {
            $result = 0;
        }
        return $result;
    }
?>