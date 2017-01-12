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

import ActivateAccount from '../../../components/portal/ActivateAccount';
import React from 'react';
import { expect } from 'chai';
import { stub, spy } from 'sinon';
import { shallow } from 'enzyme';
import userStore from '../../../store/userStore';

describe('ActivateAccount', () => {
  it('handles activation failure', () => {
    const replace = spy();
    stub(userStore, 'getStateValue', () => ({ success: false }));

    const cmp = shallow(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />, {
      context: {
        router: {
          replace
        }
      }
    });

    cmp.instance()._handleChange();
    cmp.update();

    expect(cmp.find('DismissableAlertBox').prop('bsStyle')).to.equal('danger');
    expect(replace.called).to.equal(false);

    userStore.getStateValue.restore();
  });

  it('activates user accounts', () => {
    const replace = spy();
    stub(userStore, 'getStateValue', () => ({ success: true }));

    const cmp = shallow(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />, {
      context: {
        router: {
          replace
        }
      }
    });

    cmp.instance()._handleChange();
    cmp.update();
    expect(cmp.find('DismissableAlertBox').prop('bsStyle')).to.equal('success');

    expect(replace.calledOnce).to.equal(true);
    expect(replace.calledWith('/')).to.equal(true);

    userStore.getStateValue.restore();
  });
});
