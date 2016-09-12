/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Form from '../../../../components/portal/signup/Form';
import React from 'react';
import { stub } from 'sinon';
import { expect } from 'chai';
import AccountWebAPIUtils from '../../../../util/api/AccountWebAPIUtils';
import { shallow } from 'enzyme';
import Locale from '../../../../util/http/LocaleService';
import RegistrationStore from '../../../../store/RegistrationStore';
import Success from '../../../../components/portal/signup/Success';

describe('Form', () => {
  it('handles invalid data and renders its errors into the markup', () => {
    const suggestions = ['Ma27_2016'],
        errors        = {
      username: {
        en: ['Username in use!']
      }
    };

    stub(RegistrationStore, 'getState', () => ({
      errors,
      suggestions
    }));

    stub(Locale, 'getLocale', () => 'en');
    const cmp = shallow(<Form />);
    cmp.setState({
      data: {
        username:      'Ma27',
        password:      '123456',
        email:         'foo@bar.de',
        locale:        'de',
        recaptchaHash: 'recaptcha-hash'
      }
    });

    cmp.instance()._handleChange();
    expect(cmp.contains(<Success />)).to.equal(false);
    expect(cmp.find('Suggestions').prop('suggestions')).to.equal(suggestions);
    expect(cmp.find('form > [name="username"]').prop('errors')).to.equal(errors);

    Locale.getLocale.restore();
    RegistrationStore.getState.restore();
  });

  it('shows success', () => {
    stub(Locale, 'getLocale', () => 'en');
    stub(RegistrationStore, 'getState', () => null);

    const cmp = shallow(<Form />);

    cmp.setState({
      data: {
        username:      'Ma27',
        password:      '123456',
        email:         'foo@bar.de',
        locale:        'de',
        recaptchaHash: 'recaptcha-hash'
      }
    });

    cmp.instance()._handleChange();
    cmp.update();

    expect(cmp.state('success')).to.equal(true);
    expect(cmp.contains(<Success />)).to.equal(true);

    Locale.getLocale.restore();
    RegistrationStore.getState.restore();
  });

  it('handles submit', () => {
    stub(AccountWebAPIUtils, 'createAccount');

    const cmp = shallow(<Form />);
    cmp.setState({
      data: {
        username:      'Ma27',
        password:      '123456',
        email:         'foo@bar.de',
        locale:        'de',
        recaptchaHash: 'recaptcha-hash'
      }
    });

    cmp.simulate('submit', { preventDefault: () => {} });
    expect(AccountWebAPIUtils.createAccount.calledOnce);
    const data = AccountWebAPIUtils.createAccount.args[0][0];

    expect(data.username).to.equal('Ma27');
    expect(data.password).to.equal('123456');
    expect(data.email).to.equal('foo@bar.de');
    expect(data.locale).to.equal('de');

    AccountWebAPIUtils.createAccount.restore();
  });
});
