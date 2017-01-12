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

import Form from '../../../../components/portal/login/Form';
import React from 'react';
import { expect } from 'chai';
import { stub, spy } from 'sinon';
import { shallow } from 'enzyme';
import userStore from '../../../../store/userStore';
import FormHelper from '../../../../util/react/FormHelper';
import axios from 'axios';

describe('Form', () => {
  it('handles errors', () => {
    stub(userStore, 'getStateValue', () => ({
      message: {
        de: 'Ungültige Zugangsdaten',
        en: 'Invalid credentials'
      }
    }));

    stub(FormHelper.prototype, 'isSubmitted', () => true);
    const cmp = shallow(<Form />);
    cmp.instance()._change();
    cmp.update();

    expect(cmp.find('SimpleErrorAlert').prop('error').en).to.equal('Invalid credentials');

    FormHelper.prototype.isSubmitted.restore();
    userStore.getStateValue.restore();
  });

  it('handles success', () => {
    stub(userStore, 'getStateValue', () => ({}));

    const replace = spy();
    const cmp      = shallow(<Form />, {
      context: {
        router: { replace }
      }
    });

    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456'
      }
    });

    cmp.instance()._change();
    expect(replace.calledOnce).to.equal(true);
    expect(replace.calledWith('/dashboard')).to.equal(true);

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
    stub(userStore, 'getStateValue', () => ({}));

    const cmp = shallow(<Form />);
    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456'
      }
    });

    cmp.simulate('submit', { preventDefault: () => {} });

    expect(axios.post.calledWith('/api/api-key.json', { login: 'Ma27', password: '123456' })).to.equal(true);

    userStore.getStateValue.restore();
    axios.post.restore();
  });
});
