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

describe('Form', () => {
  it('handles invalid data and renders its errors into the markup', () => {
    const errors =  {
      username: {
        en: ['Username in use!']
      }
    };
    const suggestions = ['Ma27_2016'];

    stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      error({
        name_suggestions: suggestions,
        errors
      });
    });

    stub(Locale, 'getLocale', () => 'en');
    const cmp = shallow(<Form />);

    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456',
        email:    'foo@bar.de',
        locale:   'de'
      }
    });

    cmp.find('form').simulate('submit', { preventDefault: () => {} });

    setTimeout(() => {
      expect(cmp.find('form').contains('Success')).to.equal(false);
      expect(cmp.find('Suggestions').prop('suggestions')).to.equal(suggestions);
      expect(cmp.find('form > [name="username"]').prop('errors')).to.equal(errors);
    });

    AccountWebAPIUtils.createAccount.restore();
    Locale.getLocale.restore();
  });

  it('shows success', () => {
    stub(AccountWebAPIUtils, 'createAccount', (data, success) => {
      success();
    });

    stub(Locale, 'getLocale', () => 'en');
    const cmp = shallow(<Form />);

    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456',
        email:    'foo@bar.de',
        locale:   'de'
      }
    });

    cmp.find('form').simulate('submit', { preventDefault: () => {} });

    setTimeout(() => {
      expect(cmp.find('form').contains('Success')).to.equal(true);
    });

    AccountWebAPIUtils.createAccount.restore();
    Locale.getLocale.restore();
  });
});
