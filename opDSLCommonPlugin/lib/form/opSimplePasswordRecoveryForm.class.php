<?php
class opSimplePasswordRecoveryForm extends opAuthMailAddressPasswordRecoveryForm
{
  public function configure()
  {
    $config = sfConfig::get('openpne_member_config');
    $choices = $config['secret_question']['Choices'];

    $this->setWidgets(array(
      'mail_address' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'mail_address' => new sfValidatorEmail(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array(
        'callback' => array($this, 'checkSecretQuestion'),
      ))
    );

    $this->widgetSchema->setNameFormat('password_recovery[%s]');
  }

  public function checkSecretQuestion($validator, $values, $arguments = array())
  {
    $configName = (opToolkit::isMobileEmailAddress($values['mail_address'])) ? 'mobile_address' : 'pc_address';
    $memberConfig = Doctrine::getTable('MemberConfig')->findOneByNameAndValue($configName, $values['mail_address']);
    if (!$memberConfig)
    {
      throw new sfValidatorError($validator, 'invalid');
    }

    $this->member = $memberConfig->Member;

    return $values;
  }
}
