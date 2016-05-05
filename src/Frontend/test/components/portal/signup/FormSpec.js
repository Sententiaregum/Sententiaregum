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
import { Locale } from '../../../../util/http/facade/HttpServices';
import mockDOMEventObject from '../../../fixtures/mockDOMEventObject';

describe('Form', () => {
  it('handles invalid data and renders its errors into the markup', () => {
    const errors =  {
      username: ['Username in use!']
    };
    const suggestions = ['Ma27_2016'];

    stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      error({
        name_suggestions: suggestions,
        errors:           errors
      });
    });

    stub(Locale, 'getLocale', () => 'en');
    const cmp = shallow(<Form />);

    const username = cmp.find('[controlId="username"] > FormControl');
    const password = cmp.find('[controlId="password"] > FormControl');
    const email    = cmp.find('[controlId="email"] > FormControl');

    username.simulate('change', mockDOMEventObject({ username: 'Ma27' }));
    password.simulate('change', mockDOMEventObject({ password: '123456' }));
    email.simulate('change', mockDOMEventObject({ email: 'foo@bar.de' }));

    cmp.find('form').simulate('submit', { preventDefault: () => {} });

    setTimeout(() => {
      expect(cmp.find('form').contains('Success')).to.equal(false);
      expect(cmp.find('[controlId="username"] > HelpBlock').prop('validationState')).to.equal('error');
    });

    AccountWebAPIUtils.createAccount.restore();
    Locale.getLocale.restore();
  });
});
