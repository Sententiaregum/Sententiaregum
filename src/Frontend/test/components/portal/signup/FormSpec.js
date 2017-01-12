/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Form from '../../../../components/portal/signup/Form';
import React from 'react';
import { stub } from 'sinon';
import { expect } from 'chai';
import { shallow } from 'enzyme';
import Locale from '../../../../util/http/Locale';
import userStore from '../../../../store/userStore';
import Success from '../../../../components/portal/signup/Success';
import axios from 'axios';

describe('Form', () => {
  it('handles invalid data and renders its errors into the markup', () => {
    const suggestions = ['Ma27_2016'],
        errors        = {
      username: {
        en: ['Username in use!']
      }
    };

    stub(userStore, 'getStateValue', () => ({
      errors,
      name_suggestions: suggestions,
      success:          false
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
    userStore.getStateValue.restore();
  });

  it('shows success', () => {
    stub(Locale, 'getLocale', () => 'en');
    stub(userStore, 'getStateValue', () => ({
      success:          true,
      errors:           {},
      name_suggestions: [],
      id:               null
    }));

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
    userStore.getStateValue.restore();
  });

  it('handles submit', () => {
    stub(axios, 'post', () => ({
      then() {
        return this;
      },
      catch() {
        return this;
      }
    }));

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

    expect(axios.post.calledWith('/api/users.json', {
      username:      'Ma27',
      password:      '123456',
      email:         'foo@bar.de',
      locale:        'de',
      recaptchaHash: 'recaptcha-hash'
    }));

    axios.post.restore();
  });
});
