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

import LoadableButtonBar from '../../../components/form/LoadableButtonBar';
import React from 'react';
import { expect } from 'chai';
import { shallow } from 'enzyme';
import counterpart from 'counterpart';
import { stub } from 'sinon';

describe('LoadableButtonBar', () => {
  it('renders a button bar', () => {
    stub(counterpart, 'translate', (arg) => arg);

    const markup = shallow(<LoadableButtonBar progress={false} btnLabel="Label" />);
    expect(markup.hasClass('form-group'));

    const btn = markup.find('button');
    expect(btn.prop('type')).to.equal('submit');
    expect(btn.hasClass('btn btn-primary spinner-btn')).to.equal(true);
    expect(btn.contains('Label')).to.equal(true);

    counterpart.translate.restore();
  });

  it('changes the progress state', () => {
    const markup = shallow(<LoadableButtonBar progress={false} btnLabel="Label" />);
    expect(markup.find('button').prop('disabled')).to.not.equal(true);

    markup.setProps({ progress: true });
    expect(markup.find('button').prop('disabled')).to.equal('disabled');
  });
});
