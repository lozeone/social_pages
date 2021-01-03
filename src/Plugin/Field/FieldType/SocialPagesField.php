<?php

namespace Drupal\social_pages\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'social_pages' field type.
 *
 * @FieldType(
 *   id = "social_pages",
 *   label = @Translation("Social Pages"),
 *   description = @Translation("Field for storing social media links"),
 *   default_widget = "social_pages_widget",
 *   default_formatter = "social_pages_formatter"
 * )
 */
class SocialPagesField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    foreach (_social_pages_field_defintions() as $key => $item) {
      $properties[$key] = DataDefinition::create('string')
        ->setLabel(new TranslatableMarkup($item['title']))
        ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
        ->setRequired(FALSE);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $fields = [];
    foreach (_social_pages_field_defintions() as $key => $item) {
      $fields[$key] = $item['field'];
    }
    $schema = [
      'columns' => $fields,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();

    foreach (_social_pages_field_defintions() as $key => $item) {

      $value = $this->get($key)->getValue();

      if (!empty($value) && $item['pattern']) {

        $message = $this->t('Invalid value.');

        $label = isset($item['type_label']) ? $item['type_label'] : 'URL';
        $message = $this->t('The value provided for %field is not a valid %label.', array('%field' => $item['title'], '%label' => $label));

        $field_constraints[$key] = [
          'Regex' => [
            'pattern' => $item['pattern'],
            'message' => $message,
          ],
        ];
      }

    }

    $constraints[] = $constraint_manager->create('ComplexData', $field_constraints);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    /*foreach(_social_pages_field_defintions() as $key => $item){
    $options[$key] = $item['title'];
    }

    $settings = $this->getSettings();

    $elements['enabled_networks'] = [
    '#type' => 'checkboxes',
    '#title' => t('Enabled Networks'),
    '#options' => $options,
    '#default_value' => $settings['enabled_networks'],
    '#description' => t('Choose all the networds to enable for this field.'),
    '#disabled' => $has_data,
    ];*/

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    foreach (_social_pages_field_defintions() as $key => $item) {
      $value = $this->get($key)->getValue();
      if (!empty($value)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
