<div class="ds-stand-nav mb-24 " data-stand="<?php echo esc_attr( $combo ); ?>">
                    <div class="row g-12">

                        <div class="col-12 pt-12 border-top"></div>

                        <div class="col-6 col-lg-3 order-lg-1">
                            <div class="wv-input-group">
                                <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-o wv-bc-t">
                                    <span class="ds-stand-info-label wv-color-w">Stand size</span>
                                    <span class="ds-stand-info-val wv-color-o me-4">
                                        <?php echo esc_html($stand['size']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Example static, replace with actual logic if needed -->
                        <div class="col-6 col-lg-3 order-lg-1">
                            <div class="wv-input-group">
                                <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4">
                                    <span class="ds-stand-info-label"><?php esc_html_e('Total stands', 'wv-addon'); ?></span>
                                    <span class="ds-stand-info-val">31</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 order-lg-2">
                           <?php
                           $cover_image = !empty($stand['cover-image'])
                              ? esc_url($stand['cover-image'])
                              : 'https://placehold.co/600x400?text=No+Image';
                           ?>
                           <div class="d-block h-100 wv-bg-c_10 br-4 p-24">
                              <div class="d-block h-100" style="background: url(<?php echo $cover_image; ?>) center/contain no-repeat;"></div>
                           </div>
                        </div>


                        <div class="col-lg-6 order-lg-1">
                            <div class="wv-input-group">
                                <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_10 wv-bc-t">
                                    <span class="ds-stand-info-label wv-color-c_95 fw-600 ls-2"><?php esc_html_e('EQUIPMENT INCLUDED', 'wv-addon'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 order-lg-2">
                            <?php foreach ($stand['included'] as $include) : ?>
                                <div class="wv-input-group mb-4">
                                    <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_10 wv-bc-t lh-1">
                                        <span class="ds-stand-info-label wv-color-c_95 fs-14 py-8">
                                            <?php echo esc_html($include['label'] . (!empty($include['value']) ? ' ' . $include['value'] : '')); ?>
                                        </span>
                                        <span class="ds-stand-info-no"><?php echo esc_html($include['qty']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Blueprints & marketing materials -->
                        <?php if (!empty($stand['blueprints'])) : ?>
                              <?php foreach ($stand['blueprints'] as $i => $blueprint) : ?>
                                 <div class="col-lg-6 order-lg-3">
                                    <div class="row g-12 align-items-stretch my-0">
                                          <div class="col-12">
                                                <div class="d-block h-100 wv-bg-c_10 br-4 p-24 d-flex align-items-center justify-content-center">
                                                   <!-- <img src="<?php echo esc_url($blueprint['img']); ?>" alt="Blueprint" class="img-fluid d-block" style=";object-fit:contain;"> -->
                                                   <img src="https://placehold.co/600x400?text=No+Bluprint" alt="Blueprint" class="img-fluid d-block" style=";object-fit:contain;">
                                                </div>
                                          </div>
                                          <?php if (!empty($blueprint['marketing_labels'])): ?>
                                            <?php foreach ($blueprint['marketing_labels'] as $j => $label) : 
                                                // Pick class based on index, fallback to empty if out of range
                                                $bg_class = $blueprint_bg_classes[$i][$j] ?? '';
                                             ?>
                                                
                                             <div class="col-12">
                                                   <div class="wv-input-group">
                                                      <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 <?php echo esc_attr($bg_class); ?> wv-bc-t">
                                                         <span class="ds-stand-info-label wv-color-w w-auto"><?php echo esc_html($label[0]); ?></span>
                                                         <span class="ds-stand-info-label wv-color-w w-auto"><?php echo esc_html($label[1]); ?></span>
                                                      </div>
                                                   </div>                                             
                                             </div>
                                             <?php endforeach; ?>
                                          <?php endif; ?>
                                    </div>
                                 </div>
                              <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="col-12 order-lg-4 my-0"></div>

                        <div class="col-lg-6 order-lg-4">
                           <div class="wv-input-group">
                              <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_50 wv-bc-t">
                                 <span class="ds-stand-info-label wv-color-w w-auto">Print files delivery address</span>
                                 <span class="ds-stand-info-label wv-color-w w-auto fw-600">office@belgradefair.rs</span>
                              </div>
                           </div>
                        </div>

                        <div class="col-lg-6 order-lg-4">
                           <div class="wv-input-group">
                              <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_50 wv-bc-t">
                                 <span class="ds-stand-info-label wv-color-w w-auto">Print files delivery deadline</span>
                                 <span class="ds-stand-info-label wv-color-w w-auto fw-600">01.01.2025.</span>
                              </div>
                           </div>
                        </div>


                     </div>
                     

                </div>