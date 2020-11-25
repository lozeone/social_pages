<?php

namespace Drupal\social_pages\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'social_pages_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "social_pages_formatter",
 *   label = @Translation("Social pages formatter"),
 *   field_types = {
 *     "social_pages"
 *   }
 * )
 */
class SocialPagesFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'mode' => 'links',
      'enabled_networks' => social_pages_field_options()
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['enabled_networks'] = [
      '#type' => 'checkboxes',
      '#title' => t('Enabled Networks'),
      '#options' => social_pages_field_options(),
      '#default_value' => $this->getSetting('enabled_networks'),
      '#description' => t('Choose all the networds to enable for this field.'),
    ];

    $elements['mode'] = [
      '#type' => 'radios',
      '#title' => t('Display as'),
      '#options' => ['links' => 'Links', 'icons' => 'Icons', 'urls' => 'Urls'],
      '#default_value' => $this->getSetting('mode'),
    ];

    return $elements + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $enabled = array_filter($this->getSetting('enabled_networks'));
    if (!empty($enabled)) {
      $summary[] = t('Enabled Networks: @networks', ['@networks' => implode(', ', $enabled)]);
      $summary[] = t('Display as: @mode', ['@mode' => $this->getSetting('mode')]);
    } else {
      $summary[] = t('No Enabled Networks');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }
    $elements['#attached']['library'][] = 'social_pages/icon-styles';

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {

    $enabled = $this->getSetting('enabled_networks');
    $enabled = array_filter($enabled);

    $mode = $this->getSetting('mode');

    $info = _social_pages_field_defintions();

    $output = '';
    foreach($enabled as $network){
      if(!empty($item->{$network})){
         $url = str_replace('!value', $item->{$network}, $info[$network]['render_pattern']);
         if($mode == 'links'){
          $output .=  '<div class="'.$network.' social-page-link"><a href="'.$url.'">' . $info[$network]['title'] . '</a></div>';
         }
         else if($mode == 'icons'){
          $output .=  '<div class="'.$network.' social-page-link"><a href="'.$url.'">' . $info[$network]['icon'] . '</a></div>';
         }
         else{
          $output .=  '<div class="'.$network.' social-page-link">'.$url.'</div>';
         }
      }
    }
    return $output;
  }

}
