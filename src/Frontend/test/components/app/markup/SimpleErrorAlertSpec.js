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

import React from 'react';
import { shallow } from 'enzyme';
import { stub } from 'sinon';
import { expect } from 'chai';
import SimpleErrorAlert from '../../../../components/app/markup/SimpleErrorAlert';

describe('SimpleErrorAlert', () => {
  it('renders a translated message into a dismissable alert box', () => {
    const cmp = shallow(<SimpleErrorAlert error={{ de: 'Fehlermeldung', en: 'Error message' }} />);
    expect(cmp.find('DismissableAlertBox').prop('bsStyle')).to.equal('danger');
    expect(cmp.find('DismissableAlertBox p').text()).to.equal('Error message');
  });
});
